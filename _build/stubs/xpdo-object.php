<?php

/**
 * Stand-in for static analysis. The real class is loaded from the MODX core.
 * The package build does not include this file.
 */
class xPDOObject
{
    /**
     * @return array
     */
    public function toArray()
    {
        return array();
    }
}
