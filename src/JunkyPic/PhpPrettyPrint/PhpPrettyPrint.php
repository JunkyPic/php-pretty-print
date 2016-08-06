<?php

namespace JunkyPic\PhpPrettyPrint;

class PhpPrettyPrint
{
    private static $debugBacktrace;
    private static $info = [];
    private static $output = "<div class=\"pretty-print\">";

    public static function dump($dump)
    {
        static::$debugBacktrace = debug_backtrace();

        // Parse the debug backtrace until the current file is reached
        foreach(static::$debugBacktrace as $key => $value)
        {
            if(isset($value['class']) && $value['class'] == __CLASS__)
            {
                // get whatever info is needed

                // open the file in which this method was called
                $file = fopen($value['file'], 'r');
                // get the name the argument passed in to ::dump
                while(($line = fgets($file)) !== false)
                {
                    // if the current line contains this method
                    if(strpos($line, __METHOD__))
                    {
                        // since only the name of the variable passed in is of interest
                        // we can just replace the method and the trailing );
                        // no need for preg_match since str_replace is faster
                        $argument = str_replace(__METHOD__ . '(', '', $line);
                        $argument = str_replace(');', '', $argument);
                        // remove pre-pended \(if any)
                        if(strpos($argument, '\\') !== false)
                        {
                            $argument = str_replace('\\', '', $argument);
                        }
                        static::$info['argument_name'] = preg_replace('~\x{00a0}~', '', preg_replace('/\s+/', '', trim($argument)));

                        // get other info of interest
                        static::$info['file'] = $value['file'];
                        static::$info['line'] = $value['line'];
                    }
                }
                fclose($file);
            }
            else
            {
                continue;
            }
        }

        switch(Types::getType($dump))
        {
            case Types::TYPE_BOOLEAN:
            case Types::TYPE_INTEGER:
            case Types::TYPE_FLOAT:
            case Types::TYPE_NULL:
            case Types::TYPE_STRING:
                static::$output .= HtmlBuilder::create()->getHtml($dump, Types::getType($dump), static::$info);
                break;
            case Types::TYPE_ARRAY:
                static::$output .= HtmlBuilder::create()->getHtml($dump, Types::TYPE_ARRAY, static::$info);
                break;
            case Types::TYPE_CALLABLE_CALLBACK:
                static::$output .= HtmlBuilder::create()->getHtml($dump, Types::TYPE_CALLABLE_CALLBACK, static::$info);
                break;
            case Types::TYPE_OBJECT:
                static::$output .= HtmlBuilder::create()->getHtml($dump, Types::TYPE_OBJECT, static::$info);
                break;

        }

        static::$output .= "</div>";
        echo '<pre>';
        echo static::$output;
        static::$output = '';
        echo '</pre>';
        die();
    }
}