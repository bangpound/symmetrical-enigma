<?php

namespace App\Model;

class KeyValue
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @var int
     */
    private $createIndex;

    /**
     * @var int
     */
    private $modifyIndex;

    /**
     * @var int
     */
    private $lockIndex;

    /**
     * @var int
     */
    private $flags;

    /**
     * @var string
     */
    private $session;

    /**
     * KeyValue constructor.
     *
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKeyWithoutParent(): string
    {
        return preg_replace('#^'.preg_quote($this->getParentKey(), '#').'#', '', $this->key);
    }

    /**
     * Boolean if the key is a "folder" or not, i.e is a nested key that feels
     * like a folder. Used for UI.
     *
     * @return bool
     */
    public function isFolder(): bool
    {
        return $this->key[\strlen($this->key) - 1] === '/';
    }

    /**
     * Boolean if the key is locked or new.
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return null !== $this->session;
    }

    /**
     * An array of the key broken up by the /
     *
     * @return string[]
     */
    public function getKeyParts(): array
    {
        $key = $this->key;

        // If the key is a folder, remove the last
        // slash to split properly
        if ($this->isFolder()) {
            $key = \substr($key, 0, -1);
        }

        return \explode('/', $key);
    }

    /**
     * The parent Key is the key one level above this.Key
     *
     * key: baz/bar/foobar/
     * grandParent: baz/bar/
     *
     * @return string
     */
    public function getParentKey(): string
    {
        $parts = $this->getKeyParts();

        // Remove the last item, essentially going up a level
        // in hierarchy
        \array_pop($parts);

        return empty($parts) ? '' : \implode('/', $parts) .'/';
    }

    /**
     * The grandParent Key is the key two levels above this.Key
     *
     * key: baz/bar/foobar/
     * grandParent: baz/
     *
     * @return string
     */
    public function getGrandParentKey(): string
    {
        $parts = $this->getKeyParts();

        // Remove the last two items, jumping two levels back
        \array_pop($parts);
        \array_pop($parts);

        return empty($parts) ? '' : \implode('/', $parts) .'/';
    }

    /**
     * @return int
     */
    public function getCreateIndex(): int
    {
        return $this->createIndex;
    }

    /**
     * @param int $createIndex
     */
    public function setCreateIndex(int $createIndex): void
    {
        $this->createIndex = $createIndex;
    }

    /**
     * @return int
     */
    public function getModifyIndex(): int
    {
        return $this->modifyIndex;
    }

    /**
     * @param int $modifyIndex
     */
    public function setModifyIndex(int $modifyIndex): void
    {
        $this->modifyIndex = $modifyIndex;
    }

    /**
     * @return int
     */
    public function getLockIndex(): int
    {
        return $this->lockIndex;
    }

    /**
     * @param int $lockIndex
     */
    public function setLockIndex(int $lockIndex): void
    {
        $this->lockIndex = $lockIndex;
    }

    /**
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * @param int $flags
     */
    public function setFlags(int $flags): void
    {
        $this->flags = $flags;
    }

    /**
     * @return string
     */
    public function getSession(): string
    {
        return $this->session;
    }

    /**
     * @param string $session
     */
    public function setSession(string $session): void
    {
        $this->session = $session;
    }
}
