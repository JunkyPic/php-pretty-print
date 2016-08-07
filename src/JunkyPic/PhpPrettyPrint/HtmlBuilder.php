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
    private $html = '';

    /**
     * @var
     */
    private $objectClone;

    /**
     * @var array
     */
    private $object = [];

    /**
     * @var array
     */
    private $cssClasses = [
        'dl' => 'dl-list',
        'dt' => 'dt-term',
        'dd' => 'dd-desc',
    ];

    /**
     * @var array
     */
    private $hyperlinkWhitelistedAttributes = [
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
        $this->buildHeader($info);
        $this->html .= "<dl class=\"php-pretty-print-info-parent\"><dt><span class=\"argument-and-type\">{$info['argument_name']} ({$type})</span></dt>";

        switch($type)
        {
            case Types::TYPE_ARRAY:
                static::buildFromArray($param);
                break;
            case Types::TYPE_OBJECT:
                static::buildFromObject($param);
                break;
            case Types::TYPE_STRING:
                static::buildFromString($param);
                break;
        }

        $this->html .= "</dl>";
        return $this->html;
    }

    /**
     * @param $param
     *
     * @throws \Exception
     */
    private function buildFromString($param)
    {
        $stringLength = strlen($param);

        $this->html .= "<dd class=" . "string-color ". $this->cssClasses['dd'] . ">(" . $stringLength . ") "  . $this->escape($param) . "</dd>";
    }

    /**
     * @param $param
     */
    private function buildFromObject($param)
    {
        $reflection = new \ReflectionObject($param);

        if($reflection->isCloneable())
        {
            $this->objectClone = clone $param;
        }
        // Get info on the object
        $this->object['In namespace'] = $reflection->inNamespace() ? 'Yes' : 'No';
        if($reflection->inNamespace())
        {
            $this->object['Class namespace'] = $reflection->getNamespaceName();
        }

        $this->object['Class name'] = $reflection->getName();

        if(null === $reflection->getConstructor())
        {
            $this->object['Class construct'] = 'Class has no construct.';
        }
        else
        {
            $this->object['Class construct'] = $reflection->getConstructor();
        }

        $this->object['Is internal'] = $reflection->isInternal() ? 'Yes' : 'No';
        $this->object['Is iterable'] = $reflection->isIterateable() ? 'Yes' : 'No';
        $this->object['Is abstract'] = $reflection->isAbstract() ? 'Yes' : 'No';
        $this->object['Is final'] = $reflection->isFinal() ? 'Yes' : 'No';
        $this->object['Is user defined'] = $reflection->isUserDefined() ? 'Yes' : 'No';
        $this->object['Is instantiable'] = $reflection->isInstantiable() ? 'Yes' : 'No';
        $this->object['Is clonable'] = $reflection->isCloneable() ? 'Yes' : 'No';
        $this->object['Is interface'] = $reflection->isInterface() ? 'Yes' : 'No';

        $this->object['Class interfaces'] = ! empty($reflection->getInterfaces()) ? $reflection->getInterfaces() : 'Class implements no interfaces';
        $this->object['Class traits'] = ! empty($reflection->getTraits()) ? $reflection->getTraits() : 'Class has no traits';
        $this->object['Class parent'] = ($reflection->getParentClass()) !== false ? $reflection->getParentClass() : 'Class has no parent';
        $this->object['Class static properties'] = ! empty($reflection->getStaticProperties()) ? $reflection->getStaticProperties() : 'Class has no static properties';
        $this->object['Class static properties'] = ! empty($reflection->getDefaultProperties()) ? $reflection->getDefaultProperties() : 'Class has no default properties';

        if(false === $reflection->getFileName())
        {
            $this->object['Defined in'] = 'Class is internal, no definition to provide.';
        }
        else
        {
            $this->object['Defined in'] = $reflection->getFileName();
        }

        if(false === $reflection->getFileName())
        {
            $this->object['Start line'] = 'Class is internal, no start line to provide.';
        }
        else
        {
            $this->object['Start line'] = $reflection->getFileName();
        }

        if(false === $reflection->getEndLine())
        {
            $this->object['End line'] = 'Class is internal, no end line to provide.';
        }
        else
        {
            $this->object['End line'] = $reflection->getEndLine();
        }

        if(false === $reflection->getDocComment())
        {
            $this->object['Doc comments'] = 'No documents to provide.';
        }
        else
        {
            $this->object['Doc comments'] = $reflection->getDocComment();
        }

        $this->object['Class constants'] = ! empty($reflection->getConstants()) ? $reflection->getConstants() : 'Class has no constants';
        // End get info
        $this->buildFromObjectInformationRecursive($this->object);
    }

    /**
     * @param array $array
     *
     * @throws \Exception
     */
    private function buildFromObjectInformationRecursive(array $array)
    {
        $this->html .= "<dd class=" . $this->cssClasses['dd'] . ">";
        $this->html .= "<dl class=" . $this->cssClasses['dl'] . ">";
        foreach($array as $key => $value)
        {
            if(Types::getType($value) === Types::TYPE_ARRAY)
            {
                $printKey = Html::escape($key);
                $this->html .= "<dt class=" . $this->cssClasses['dt'] . "><span class=\"key-values\"> {$printKey}</span>" .
                    "<span class=\"equal\"> => </span>" .
                    "<br/></dt>";
                $this->buildFromObjectInformationRecursive($value);
            }
            else
            {
                $printKey = Html::escape($key);
                $printValue = Html::escape($value);
                $this->html .= "<dt class=" . $this->cssClasses['dt'] . "><span class=\"key-values\"> {$printKey}</span>" .
                    "<span class=\"equal\"> =></span> " .
                    "{$printValue}</dt><dd class=" . $this->cssClasses['dd'] . ">";
            }
        }
        $this->html .= "</dl>";
        $this->html .= "</dd>";
    }

    /**
     * @param array $info
     */
    private function buildHeader(array $info)
    {
        $this->html .= "<div class=\"php-pretty-print-header\"><dl><dt>Where was PhpPrettyPrint::dump() called?</dt><dl><dt>File: {$info['file']}</dt><dd></dd><dt>Line: {$info['line']}</dt><dd></dd></dl></div><hr>";
    }

    /**
     * @param        $hyperlinkText
     * @param string $selfAnchorText
     *
     * @throws \Exception
     * @throws HtmlBuilderException
     */
    private function buildHyperlinkElement($hyperlinkText, $selfAnchorText = '')
    {
        if( ! is_string($hyperlinkText))
        {
            throw new HtmlBuilderException("The {$hyperlinkText} must be a string");
        }

        $hyperlink_text = Html::escape($hyperlinkText);

        $this->html = '<a ';
        foreach($this->hyperlinkWhitelistedAttributes as $key => $value)
        {
            if($key == 'href' && ! empty($selfAnchorText))
            {
                $this->html .= $key . '="' . $value . preg_replace('~\x{00a0}~', '', preg_replace('/\s+/', '', trim($selfAnchorText))) . '"';
            }
            else
            {
                $this->html .= $key . '="' . $value . '"';
            }
        }

        $this->html .= ">{$hyperlink_text}</a>";
    }

    /**
     * @param array $array
     */
    private function buildFromArray(array $array)
    {
        $this->buildFromArrayRecursive($array);
    }

    /**
     * @param $array
     *
     * @throws \Exception
     */
    private function buildFromArrayRecursive($array)
    {
        $this->html .= "<dd class=" . $this->cssClasses['dd'] . ">";
        $this->html .= "<dl class=" . $this->cssClasses['dl'] . ">";

        foreach($array as $key => $value)
        {
            if(is_callable($value) || is_object($value))
            {
                $this->html .= "<dt class=" . $this->cssClasses['dt'] . ">Object</dt>";
                $this->buildFromObject($value);
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
                        $this->html .= "<dt class=" . $this->cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} ({$lengthKey}) \"{$printKey}\"</span>" .
                            "<span class=\"equal\"> => </span>" .
                            "{$typeValue}({$lengthValue}) <br/></dt>";
                    }
                    else
                    {
                        $this->html .= "<dt class=" . $this->cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} {$printKey}</span>" .
                            "<span class=\"equal\"> => </span>" .
                            "{$typeValue}({$lengthValue}) <br/></dt>";
                    }

                    $this->buildFromArrayRecursive($value);
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
                            $this->html .= "<dt class=" . $this->cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} ({$lengthKey}) \"{$printKey}\"</span>" .
                                "<span class=\"equal\"> => </span>" .
                                "{$typeValue} ({$lengthValue})\"{$printValue}\"</dt><dd class=" . $this->cssClasses['dd'] . "></dd>";
                        }
                        else
                        {
                            $this->html .= "<dt class=" . $this->cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} ({$lengthKey}) \"{$printKey}\"</span>" .
                                "<span class=\"equal\"> => </span>" .
                                "{$typeValue} {$printValue}</dt><dd class=" . $this->cssClasses['dd'] . "></dd>";
                        }
                    }
                    else
                    {
                        if($typeValue == 'string')
                        {
                            $this->html .= "<dt class=" . $this->cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} {$printKey}</span>" .
                                "<span class=\"equal\"> => </span>" .
                                "{$typeValue} ({$lengthValue})\"{$printValue}\"</dt><dd class=" . $this->cssClasses['dd'] . "></dd>";
                        }
                        else
                        {
                            $this->html .= "<dt class=" . $this->cssClasses['dt'] . "><span class=\"key-values\">{$typeKey} {$printKey}</span>" .
                                "<span class=\"equal\"> => </span>" .
                                "{$typeValue} {$printValue}</dt><dd class=" . $this->cssClasses['dd'] . "></dd>";
                        }
                    }
                }
            }
        }
        $this->html .= "</dl>";
        $this->html .= "</dd>";
    }
}