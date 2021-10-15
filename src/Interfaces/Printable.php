<?php
/**
 * @package coolrunner-utils
 * @copyright 2021
 */

namespace CoolRunner\Utils\Interfaces;

interface Printable
{
    /**
     * returns the pdf for print
     *
     * @return string
     */
    public function getPdf() : string;

    /**
     * returns the pdf-name for the pdf-print
     *
     * @return string
     */
    public function getPdfName() : string;
}
