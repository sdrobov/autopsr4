<?php

namespace AutoPsr4;


class Parser
{
    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var string
     */
    protected $rootNs;

    /**
     * @var File[]
     */
    protected $files;

    /**
     * @var ClassEntity[]
     */
    protected $classes;

    /**
     * Parser constructor.
     * @param string $rootDir
     * @param string $rootNs
     */
    public function __construct($rootDir, $rootNs)
    {
        $this->rootDir = $rootDir;
        $this->rootNs = $rootNs;
    }

    public function parse()
    {
        $this->scanDirRecursive($this->rootDir);
    }

    /**
     * @return File[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return ClassEntity[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param string $dirname
     */
    protected function scanDirRecursive($dirname)
    {
        foreach (glob("{$dirname}/*") as $entity) {
            if (is_dir($entity)) {
                $this->scanDirRecursive($entity);

                continue;
            }

            if (preg_match('/\.php$/', $entity)) {
                $entity = str_replace(dirname($this->rootDir) . '/', '', $entity);
                $file = new File($entity);

                if ($file->parse($this->rootNs)) {
                    $this->files[] = $file;
                    $this->classes[] = clone $file->getClass();
                }
            }
        }
    }
}
