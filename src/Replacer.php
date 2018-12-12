<?php

namespace AutoPsr4;


class Replacer
{
    /**
     * @var File[]
     */
    protected $files;

    /**
     * Replacer constructor.
     * @param File[] $files
     */
    public function __construct(array $files)
    {
        $this->files = $files;
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
     * @return Replacer
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @param bool $dryRun
     */
    public function replace($dryRun = false)
    {
        foreach ($this->files as $file) {
            $content = $file->getContent();
            $class = $file->getClass();

            $content = $class->addNamespace($content);
            $content = $class->renameClassname($content);
            $content = $class->renameUsage($content, false);

            $uses = [];
            foreach ($file->getUsages() as $usage) {
                if (!$class->isNsEq($usage)) {
                    $uses[] = $usage;
                }

                $content = $usage->renameUsage($content);
            }

            if (!empty($uses)) {
                $uses = implode(
                    "\n",
                    array_map(
                        function ($use) {
                            /** @var ClassEntity $use */
                            return "use {$use->getFqn()}" . ($use->getAlias() ? " as {$use->getAlias()};" : ';');
                        },
                        $uses
                    )
                );

                $content = preg_replace(
                    '/^^(\/\*(?:[^\/]|\n)+\*\/\n)?(abstract )?(class|interface|trait)/m',
                    "{$uses}\n\n$1$2$3",
                    $content
                );
            }

            $file->setContent($content);

            if (!$dryRun) {
                $file->flush();
            }
        }
    }
}
