<?php

namespace Junky\PhpPrettyPrint;
use Junky\Html\PhpPrettyPrint\HtmlBuilder;
use Junky\Types\PhpPrettyPrint\Types;

/**
 * Class PhpPrettyPrint
 *
 * @package JunkyPic\PhpPrettyPrint
 */
class PhpPrettyPrint
{
    /**
     * @var
     */
    private static $debugBacktrace;

    /**
     * @var string
     */
    private static $output = "<div class=\"pretty-print\">";

    /**
     * @param $dump
     * @param array $options
     */
    public static function dump($dump, array $options =[])
    {
        $settings = json_decode(file_get_contents(__DIR__ . '/../../settings.json'), true);

        if(is_null($settings))
        {
            $error = "Settings file is invalid.<br>Possible problems:<br>";
            $error .= " - The file doesn't exist, in which case feel free to create one yourself, or just copy it from <a target='_blank' href='https://gist.github.com/JunkyPic/4adbf5495845f4bb3ee684fd1f5078fa'>here</a>";
            $error .= " and place in the src folder.<br>";
            $error .= " - The json format is invalid. A tutorial on how to write valid json can be found <a target='_blank' href=\"http://lmgtfy.com/?q=how+to+create+json\">here</a>";
            die($error);
        }

        $css = isset($settings['theme']) ? file_get_contents(__DIR__ . '/../Themes/' . $settings['theme'] . '.css') : '';

        if(isset($settings['remove-git-link']) && $settings['remove-git-link'] == false)
        {
            static::$output .= "<dl><dt><a class=\"github\" target=\"_blank\" href=\"https://github.com/JunkyPic/php-pretty-print\">Visit on Github</a></dt></dl></div>";
        }

        static::$debugBacktrace = debug_backtrace();

        // Parse the debug backtrace until the current file is reached
        foreach(static::$debugBacktrace as $key => $value)
        {
            if(isset($value['class']) && $value['class'] == __CLASS__)
            {
                // get whatever info is needed

                // in case anybody's confused about what the following code does:
                // it parses the file in which ::dump was called and searches
                // for the argument passed in. The idea is the to get argument
                // as a string literal, as in pass $array get the string literal
                // '$array' back, NOT the value of the $array variable

                // open the file in which this method was called
                $file = fopen($value['file'], 'r');
                // get the name the argument passed in to ::dump
                while(($line = fgets($file)) !== false)
                {
                    // explode the class name
                    $explode = explode('\\', $value['class']);
                    // get the last entry, meaning exactly the class name
                    // and append the method
                    $functionName = end($explode) . '::dump';

                    // if the current line contains this method
                    if(strpos($line, $functionName) !== false)
                    {
                        // since only the name of the variable passed in is of interest
                        // we can just replace the method and the trailing );
                        if(strpos($line, $functionName) !== false)
                        {
                            $argument = substr($line, strpos($line, $functionName));
                            $argument = str_replace([ '\\', $functionName . '(', ');', '/',], '', $argument);
                            $options['argument_name'] = preg_replace('~\x{00a0}~', '', preg_replace('/\s+/', '', trim($argument)));
                        }

                        // get other info of interest
                        $options['file'] = $value['file'];
                        $options['line'] = $value['line'];
                        break;
                    }
                }
                fclose($file);
            }
            else
            {
                continue;
            }
        }

        static::$output .= HtmlBuilder::create()->getHtml($dump, Types::getType($dump), $options);

        static::$output .= "</div>";

        if(isset($settings['pre-tags']) && $settings['pre-tags'] == true)
        {
            static::$output .= "<style>{$css}</style>";
            echo '<pre>';
            echo static::$output;
            static::$output = '';
            echo '</pre>';
            die();
        }

        static::$output .= "<style>{$css}</style>";
        echo static::$output;
        static::$output = '';
        die();
    }
}
