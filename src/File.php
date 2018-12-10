<?php

namespace AutoPsr4;


class File
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var ClassEntity
     */
    protected $class;

    /**
     * @var ClassEntity[]
     */
    protected $usages = [];

    /**
     * File constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $parts = explode(PATH_SEPARATOR, $path);
        $this->name = end($parts);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return ClassEntity
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param ClassEntity $class
     * @return File
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return ClassEntity[]
     */
    public function getUsages()
    {
        return $this->usages;
    }

    /**
     * @param ClassEntity[] $usages
     * @return File
     */
    public function setUsages($usages)
    {
        $this->usages = $usages;

        return $this;
    }

    /**
     * @param ClassEntity $usage
     * @return File
     */
    public function addUsage(ClassEntity $usage)
    {
        foreach ($this->usages as $item) {
            if ($item->isEq($usage)) {
                return $this;
            }

            if ($item->getShortClassName() == $usage->getShortClassName()) {
                $usedAliases = array_map(function ($item) {
                    /** @var ClassEntity $item */
                    return $item->getAlias() ?: null;
                }, $this->usages);

                array_filter($usedAliases);

                $usage->generateAlias($usedAliases);
            }
        }

        $this->usages[] = $usage;

        return $this;
    }

    /**
     * @param string $rootNs
     * @return bool
     */
    public function parse($rootNs)
    {
        $parts = explode('/', $this->path);
        array_shift($parts);

        $possibleClassName = array_pop($parts);
        $possibleClassName = str_replace('.php', '', $possibleClassName);

        $ns = $rootNs . (empty($parts) ? '' : '\\' . implode("\\", $parts));

        $content = file_get_contents($this->path);

        $match = [];
        if (preg_match('/^namespace ([^;]+);$/m', $content, $match)) {
            return false;
        }

        $match = [];
        if (!preg_match('/^(?:abstract )?(?:class|interface|trait) (\w+)/m', $content, $match)) {
            return false;
        }

        $oldClassName = $match[1];
        $classParts = explode('_', $oldClassName);
        $realClassName = array_pop($classParts);

        if ($realClassName != $possibleClassName) {
            return false;
        }

        $this->class = (new ClassEntity())->setFqn("{$ns}\\{$realClassName}")
            ->setOldClassName($oldClassName)
            ->setShortClassName($realClassName)
            ->setNs($ns);

        return true;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->path) ?: '';
    }

    /**
     * @param string $content
     * @return File
     */
    public function setContent($content)
    {
        file_put_contents($this->path, $content);

        return $this;
    }
}
