<?php

namespace AutoPsr4;


class ClassEntity
{
    /**
     * @var string
     */
    protected $fqn;

    /**
     * @var string
     */
    protected $ns;

    /**
     * @var string
     */
    protected $shortClassName;

    /**
     * @var string
     */
    protected $oldClassName;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @return string
     */
    public function getFqn()
    {
        return $this->fqn;
    }

    /**
     * @param string $fqn
     * @return ClassEntity
     */
    public function setFqn($fqn)
    {
        $this->fqn = $fqn;

        return $this;
    }

    /**
     * @return string
     */
    public function getNs()
    {
        return $this->ns;
    }

    /**
     * @param string $ns
     * @return ClassEntity
     */
    public function setNs($ns)
    {
        $this->ns = $ns;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortClassName()
    {
        return $this->shortClassName;
    }

    /**
     * @param string $shortClassName
     * @return ClassEntity
     */
    public function setShortClassName($shortClassName)
    {
        $this->shortClassName = $shortClassName;

        return $this;
    }

    /**
     * @return string
     */
    public function getOldClassName()
    {
        return $this->oldClassName;
    }

    /**
     * @param string $oldClassName
     * @return ClassEntity
     */
    public function setOldClassName($oldClassName)
    {
        $this->oldClassName = $oldClassName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return ClassEntity
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @param ClassEntity $classEntity
     * @return bool
     */
    public function isEq(ClassEntity $classEntity)
    {
        return $this->fqn == $classEntity->getFqn();
    }

    /**
     * @param ClassEntity $classEntity
     * @return bool
     */
    public function isNsEq(ClassEntity $classEntity)
    {
        return $this->ns == $classEntity->getNs();
    }

    /**
     * @param string[] $usedAliases
     * @return ClassEntity
     */
    public function generateAlias(array $usedAliases = [])
    {
        $parts = explode('\\', $this->ns);

        if (count($parts) > 1) {
            $lastNs = end($parts);
            $alias = "{$lastNs}{$this->shortClassName}";
        } else {
             $alias = "{$this->shortClassName}1";
        }

        if (!empty($usedAliases)) {
            while (in_array($alias, $usedAliases)) {
                $alias = $this->shortClassName . mt_rand(100, 999);
            }
        }

        return $this->setAlias($alias);
    }
}
