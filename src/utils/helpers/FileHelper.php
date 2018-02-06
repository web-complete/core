<?php

namespace WebComplete\core\utils\helpers;

class FileHelper
{

    /**
     * Slightly modified version of http://www.geekality.net/2011/05/28/php-tail-tackling-large-files/
     *
     * @author Torleif Berger, Lorenzo Stanco
     * @link http://stackoverflow.com/a/15025877/995958
     * @license http://creativecommons.org/licenses/by/3.0/
     *
     * @param string $filepath
     * @param int $lines
     * @param bool $adaptive
     *
     * @return bool|string
     */
    public static function tail(string $filepath, int $lines = 1, $adaptive = true)
    {
        // Open file
        $file = @fopen($filepath, "rb");
        if ($file === false) {
            return false;
        }
        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) {
            $buffer = 4096;
        } else {
            $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));
        }
        // Jump to last character
        fseek($file, -1, SEEK_END);
        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($file, 1) != "\n") {
            $lines -= 1;
        }

        // Start reading
        $output = '';
        // While we would like more
        while (ftell($file) > 0 && $lines >= 0) {
            // Figure out how far back we should jump
            $seek = min(ftell($file), $buffer);
            // Do the jump (backwards, relative to where we are)
            fseek($file, -$seek, SEEK_CUR);
            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($file, $seek)) . $output;
            // Jump back to where we started reading
            fseek($file, -mb_strlen($chunk, '8bit'), SEEK_CUR);
            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");
        }
        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {
            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);
        }
        // Close file and return
        fclose($file);
        return trim($output);
    }
}
