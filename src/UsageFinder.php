<?php

namespace AutoPsr4;


class UsageFinder
{
    /**
     * @var File[]
     */
    protected $files;

    /**
     * @var ClassEntity[]
     */
    protected $classes;

    /**
     * UsageFinder constructor.
     * @param File[] $files
     * @param ClassEntity[] $classes
     */
    public function __construct(array $files, array $classes)
    {
        $this->files = $files;
        $this->classes = $classes;
    }

    /**
     * @return File[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param File[] $files
     * @return UsageFinder
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return ClassEntity[]
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param ClassEntity[] $classes
     * @return UsageFinder
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;

        return $this;
    }

    public function findUsages()
    {
        foreach ($this->files as &$file) {
            foreach ($this->classes as $class) {
                if ($file->getClass()->isEq($class)) {
                    $file->addUsage($class);

                    continue;
                }

                $match = [];
                if (
                preg_match_all(
                    "/(?<!class )(?<!trait )(?<!interface )(?<!namespace )(?<!\$)(?<!\w){$class->getOldClassName()}(?!\w)/",
                    $file->getContent(),
                    $match
                )
                ) {
                    $file->addUsage($class);
                }
            }
        }
    }
}
