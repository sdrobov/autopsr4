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

    public function replace()
    {
        foreach ($this->files as $file) {
            $content = $file->getContent();
            $class = $file->getClass();

            $content = preg_replace(
                '/\<\?(php)?\n(\n?\/\*(?:[^\/]|\n)+\*\/\n)?/ms',
                "<?php\n$2\n\nnamespace {$class->getNs()};\n\n",
                $content
            );
            $content = preg_replace(
                '/^(abstract )?(class|interface) \w+/m',
                "$1$2 {$class->getShortClassName()}",
                $content,
                1
            );

            $uses = [];
            foreach ($file->getUsages() as $usage) {
                if (!$class->isNsEq($usage)) {
                    $uses[] = $usage;
                }

                $content = preg_replace(
                    "/(?<!class )(?<!interface )(?<!namespace )(?<!\$)(?<!\\\\)(?<!\w)(?<!')(?<!\"){$usage->getOldClassName()}(?!\w)/",
                    $usage->getAlias() ?: $usage->getShortClassName(),
                    $content
                );
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
        }
    }
}
