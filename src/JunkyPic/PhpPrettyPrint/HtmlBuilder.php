<?php

namespace JunkyPic\PhpPrettyPrint;

use JunkyPic\PhpPrettyPrint\Exception\HtmlBuilderException;

/**
 * Class HtmlBuilder
 *
 * @package JunkyPic\PhpPrettyPrint
 */
class HtmlBuilder extends Html
{
    /**
     * @var string
     */
    private static $html = '';

    /**
     * @var
     */
    private static $objectClone;

    /**
     * @var array
     */
    private static $object = [];

    /**
     * @var array
     */
    private static $cssClasses = [
        'dl' => 'dl-list',
        'dt' => 'dt-term',
        'dd' => 'dd-desc',
    ];

    /**
     * @var array
     */
    private static $hyperlinkWhitelistedAttributes = [
        'href'   => '#',
        'target' => '_self',
        'type'   => 'text/html',
    ];

    /**
     * @param       $param
     * @param       $type
     * @param array $info
     *
     * @return string
     */
    public function getHtml($param, $type, array $info)
    {
        static::buildHeader($info);
        static::$html .= "<dl class=\"php-pretty-print-info-parent\"><dt><span class=\"argument-and-type\">{$info['argument_name']} ({$type})</span></dt>";

        switch($type)
        {
            case Types::TYPE_ARRAY:
                static::buildFromArray($param);
                break;
            case Types::TYPE_OBJECT:
                static::buildFromObject($param);
                break;
        }

        static::$html .= "</dl>";
        $out = static::$html;
        static::$html = '';

        return $out;
    }

    /**
     * @param $param
     */
    private static function buildFromObject($param)
    {
        $reflection = new \ReflectionObject($param);

        if($reflection->isCloneable())
        {
            static::$objectClone = clone $param;
        }
        // Get info on the object
        static::$object['In namespace'] = $reflection->inNamespace() ? 'Yes' : 'No';
        if($reflection->inNamespace())
        {
            static::$object['Class namespace'] = $reflection->getNamespaceName();
        }

        static::$object['Class name'] = $reflection->getName();

        if(null === $reflection->getConstructor())
        {
            static::$object['Class construct'] = 'Class has no construct.';
        }
        else
        {
            static::$object['Class construct'] = $reflection->getConstructor();
        }

        static::$object['Is internal'] = $reflection->isInternal() ? 'Yes' : 'No';
        static::$object['Is iterable'] = $reflection->isIterateable() ? 'Yes' : 'No';
        static::$object['Is abstract'] = $reflection->isAbstract() ? 'Yes' : 'No';
        static::$object['Is final'] = $reflection->isFinal() ? 'Yes' : 'No';
        static::$object['Is user defined'] = $reflection->isUserDefined() ? 'Yes' : 'No';
        static::$object['Is instantiable'] = $reflection->isInstantiable() ? 'Yes' : 'No';
        static::$object['Is clonable'] = $reflection->isCloneable() ? 'Yes' : 'No';
        static::$object['Is interface'] = $reflection->isInterface() ? 'Yes' : 'No';

        static::$object['Class interfaces'] = ! empty($reflection->getInterfaces()) ? $reflection->getInterfaces() : 'Class implements no interfaces';
        static::$object['Class traits'] = ! empty($reflection->getTraits()) ? $reflection->getTraits() : 'Class has no traits';
        static::$object['Class parent'] = ($reflection->getParentClass()) !== false ? $reflection->getParentClass() : 'Class has no parent';
        static::$object['Class static properties'] = ! empty($reflection->getStaticProperties()) ? $reflection->getStaticProperties() : 'Class has no static properties';
        static::$object['Class static properties'] = ! empty($reflection->getDefaultProperties()) ? $reflection->getDefaultProperties() : 'Class has no default properties';

        if(false === $reflection->getFileName())
        {
            static::$object['Defined in'] = 'Class is internal, no definition to provide.';
        }
        else
        {
            static::$object['Defined in'] = $reflection->getFileName();
        }

        if(false === $reflection->getFileName())
        {
            static::$object['Start line'] = 'Class is internal, no start line to provide.';
        }
        else
        {
            static::$object['Start line'] = $reflection->getFileName();
        }

        if(false === $reflection->getEndLine())
        {
            static::$object['End line'] = 'Class is internal, no end line to provide.';
        }
        else
        {
            static::$object['End line'] = $reflection->getEndLine();
        }

        if(false === $reflection->getDocComment())
        {
            static::$object['Doc comments'] = 'No documents to provide.';
        }
        else
        {
            static::$object['Doc comments'] = $reflection->getDocComment();
        }

        static::$object['Class constants'] = ! empty($reflection->getConstants()) ? $reflection->getConstants() : 'Class has no constants';
        // End get info
        static::buildFromObjectInformationRecursive(static::$object);
    }

    /**
     * @param array $array
     *
     * @throws Exception\OutputException
     */
    private static function buildFromObjectInformationRecursive(array $array)
    {
        static::$html .= "<dd class=" . static::$cssClasses['dd'] . ">";
        static::$html .= "<dl class=" . static::$cssClasses['dl'] . ">";
        foreach($array as $key => $value)
        {
            if(Types::getType($value) === Types::TYPE_ARRAY)
            {
                $printKey = Html::escape($key);
                static::$html .= "<dt class=" . static::$cssClasses['dt'] . "><span class=\"key-values\"> {$printKey}</span>" .
                    "<span class=\"equal\"> => </span>" .
                    "<br/></dt>";
                static::buildFromObjectInformationRecursive($value);
            }
            else
            {
                $printKey = Html::escape($key);
                $printValue = Html::escape($value);
                static::$html .= "<dt class=" . static::$cssClasses['dt'] . "><span class=\"key-values\"> {$printKey}</span>" .
                    "<span class=\"equal\"> =></span> " .
                    "{$printValue}</dt><dd class=" . static::$cssClasses['dd'] . ">";
            }
        }
        static::$html .= "</dl>";
        static::$html .= "</dd>";
    }

    /**
     * @param array $info
     */
    private static function buildHeader(array $info)
    {
        static::$html .= "<div class=\"php-pretty-print-header\"><dl><dt>Where was PhpPrettyPrint::dump() called?</dt><dl><dt>File: {$info['file']}</dt><dd></dd><dt>Line: {$info['line']}</dt><dd></dd></dl></div><hr>";
    }

    /**
     * @param        $hyperlinkText
     * @param string $selfAnchorText
     *
     * @throws Exception\OutputException
     * @throws HtmlBuilderException
     */
    private static function buildHyperlinkElement($hyperlinkText, $selfAnchorText = '')
    {
        if( ! is_string($hyperlinkText))
        {
            throw new HtmlBuilderException("The {$hyperlinkText} must be a string");
        }

        $hyperlink_text = Html::escape($hyperlinkText);

        static::$html = '<a ';
        foreach(static::$hyperlinkWhitelistedAttributes as $key => $value)
        {
            if($key == 'href' && ! empty($selfAnchorText))
            {
                static::$html .= $key . '="' . $value . preg_replace('~\x{00a0}~', '', preg_replace('/\s+/', '', trim($selfAnchorText))) . '"';
            }
            else
            {
                static::$html .= $key . '="' . $value . '"';
            }
        }

        static::$html .= ">{$hyperlink_text}</a>";
    }

    /**
     * @param array $array
     */
    private static function buildFromArray(array $array)
    {
        static::buildFromArrayRecursive($array);
    }

    /**
     * @param $array
     *
     * @throws Exception\OutputException
     */
    private static function buildFromArrayRecursive($array)
    {
        static::$html .= "<dd class=" . static::$cssClasses['dd'] . ">";
        static::$html .= "<dl class=" . static::$cssClasses['dl'] . ">";

        foreach($array as $key => $value)
        {
            if(is_callable($value) || is_object($value))
            {
                static::$html .= "<dt class=" . static::$cssClasses['dt'] . ">Object</dt>";
                static::buildFromObject($value);
            }
            else
            {
                if(Types::getType($value) === Types::TYPE_ARRAY)
                {
                    $typeKey = gettype($key);
                    $typeValue = gettype($value);
                    $lengthValue = count($value);
                    $printKey = Html::escape($key);

                    $lengthKey = strlen($key);

                    if($typeKey == 'string')
                    {
                        static::$html .= "<dt class=" . static::$cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} ({$lengthKey}) \"{$printKey}\"</span>" .
                            "<span class=\"equal\"> => </span>" .
                            "{$typeValue}({$lengthValue}) <br/></dt>";
                    }
                    else
                    {
                        static::$html .= "<dt class=" . static::$cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} {$printKey}</span>" .
                            "<span class=\"equal\"> => </span>" .
                            "{$typeValue}({$lengthValue}) <br/></dt>";
                    }

                    static::buildFromArrayRecursive($value);
                }
                else
                {

                    $printKey = Html::escape($key);
                    $printValue = Html::escape($value);

                    $typeKey = gettype($key);
                    $typeValue = gettype($value);

                    $lengthValue = strlen($value);
                    $lengthKey = strlen($key);

                    if($typeKey == 'string')
                    {
                        if($typeValue == 'string')
                        {
                            static::$html .= "<dt class=" . static::$cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} ({$lengthKey}) \"{$printKey}\"</span>" .
                                "<span class=\"equal\"> => </span>" .
                                "{$typeValue} ({$lengthValue})\"{$printValue}\"</dt><dd class=" . static::$cssClasses['dd'] . "></dd>";
                        }
                        else
                        {
                            static::$html .= "<dt class=" . static::$cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} ({$lengthKey}) \"{$printKey}\"</span>" .
                                "<span class=\"equal\"> => </span>" .
                                "{$typeValue} {$printValue}</dt><dd class=" . static::$cssClasses['dd'] . "></dd>";
                        }
                    }
                    else
                    {
                        if($typeValue == 'string')
                        {
                            static::$html .= "<dt class=" . static::$cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} {$printKey}</span>" .
                                "<span class=\"equal\"> => </span>" .
                                "{$typeValue} ({$lengthValue})\"{$printValue}\"</dt><dd class=" . static::$cssClasses['dd'] . "></dd>";
                        }
                        else
                        {
                            static::$html .= "<dt class=" . static::$cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} {$printKey}</span>" .
                                "<span class=\"equal\"> => </span>" .
                                "{$typeValue} {$printValue}</dt><dd class=" . static::$cssClasses['dd'] . "></dd>";
                        }
                    }
                }
            }
        }
        static::$html .= "</dl>";
        static::$html .= "</dd>";
    }
}