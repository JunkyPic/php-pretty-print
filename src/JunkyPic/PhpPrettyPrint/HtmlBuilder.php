<?php

namespace JunkyPic\PhpPrettyPrint;

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
     * @var array
     */
    private $object = [];

    /**
     * @var array
     */
    private $cssClasses = [
        'dl' => 'd-list',
        'dt' => 'd-term',
        'dd' => 'd-desc',
    ];

    /**
     * @param       $param
     * @param       $type
     * @param array $options
     *
     * @return string
     */
    public function getHtml($param, $type, array $options)
    {
        $this->buildHeader($options);
        $this->html .=
                "<dl class=\"php-pretty-print-info-parent\">" .
                    "<dt>" .
                    "<span class=\"argument-and-type\">{$options['argument_name']} ({$type})</span>" .
                    "</dt>";
                    // tag closes bellow
        switch($type)
        {
            case Types::TYPE_BOOLEAN:
                $this->buildFromBoolean($param);
                break;
            case Types::TYPE_INTEGER:
                $this->buildFromInteger($param);
                break;
            case Types::TYPE_FLOAT:
                $this->buildFromFloat($param);
                break;
            case Types::TYPE_RESOURCE:
                $this->buildFromResource($param);
                break;
            case Types::TYPE_NULL:
                $this->buildFromNull($param);
                break;
            case Types::TYPE_ARRAY:
                $this->buildFromArray($param, $options);
                break;
            case Types::TYPE_OBJECT:
                // Parameter $options is purposely left out of this function
                // objects can be internal and we don't want to truncate
                // any sort of internal information provided in case
                // the excerpt is present in the options array
                $this->buildFromObject($param);
                break;
            case Types::TYPE_STRING:
                $this->buildFromString($param, $options);
                break;
        }

        $this->html .= "</dl>";

        return $this->html;
    }

    /**
     * @param $param
     *
     * @throws \Exception
     */private function buildFromResource($param)
    {
        $this->html .=
            "<dd class=\"" . "css-type-resource " . $this->cssClasses['dd'] . "\">" .
            Types::TYPE_RESOURCE . " " .
            $this->escape($param) .
            "</dd>";
    }

    /**
     * @param $param
     *
     * @throws \Exception
     */private function buildFromNull($param)
    {
        $this->html .=
            "<dd class=\"" . "css-type-null " . $this->cssClasses['dd'] . "\">" .
            Types::TYPE_NULL . " " .
            $this->escape($param) .
            "</dd>";
    }

    /**
     * @param $param
     *
     * @throws \Exception
     */private function buildFromFloat($param)
    {
        $this->html .=
            "<dd class=\"" . "css-type-float " . $this->cssClasses['dd'] . "\">" .
            Types::TYPE_FLOAT . " " .
            $this->escape($param) .
            "</dd>";
    }

    /**
     * @param $param
     *
     * @throws \Exception
     */private function buildFromInteger($param)
    {
        $this->html .=
            "<dd class=\"" . "css-type-integer " . $this->cssClasses['dd'] . "\">" .
            Types::TYPE_INTEGER . " " .
            $this->escape($param) .
            "</dd>";
    }

    /**
     * @param $param
     *
     * @throws \Exception
     */private function buildFromBoolean($param)
    {
        $this->html .=
            "<dd class=\"" . "css-type-boolean " . $this->cssClasses['dd'] ."\">" .
            Types::TYPE_BOOLEAN . " " .
            $this->escape($param).
            "</dd>";
    }

    /**
     * @param $param
     * @param $options
     *
     * @throws \Exception
     */
    private function buildFromString($param, array $options)
    {
        $stringLength = strlen($param);

        $this->html .=
            "<dd class=\"" . "css-type-string " . $this->cssClasses['dd'] . "\">" .
            "(" . $stringLength . ") " .
            $this->getExcerpt($this->escape($param), $options) .
            "</dd>";
    }

    /**
     * @param $param
     */
    private function buildFromObject($param)
    {
        $reflection = new \ReflectionObject($param);

        // Get info on the object
        $this->object['In namespace'] = $reflection->inNamespace() ? 'Yes' : 'No';
        if($reflection->inNamespace())
        {
            $this->object['Class namespace'] = $reflection->getNamespaceName();
        }

        $this->object['Class name'] = $reflection->getName();

        $this->object['Is internal'] = $reflection->isInternal() ? 'Yes' : 'No';
        $this->object['Is iterable'] = $reflection->isIterateable() ? 'Yes' : 'No';
        $this->object['Is abstract'] = $reflection->isAbstract() ? 'Yes' : 'No';
        $this->object['Is final'] = $reflection->isFinal() ? 'Yes' : 'No';
        $this->object['Is user defined'] = $reflection->isUserDefined() ? 'Yes' : 'No';
        $this->object['Is instantiable'] = $reflection->isInstantiable() ? 'Yes' : 'No';
        $this->object['Is clonable'] = $reflection->isCloneable() ? 'Yes' : 'No';
        $this->object['Is interface'] = $reflection->isInterface() ? 'Yes' : 'No';
        $this->object['Class constants'] = ! empty($reflection->getConstants()) ? $reflection->getConstants() : 'Class has no constants';
        $this->object['Class static properties'] = ! empty($reflection->getStaticProperties()) ? $reflection->getStaticProperties() : 'Class has no static properties';
        $this->object['Class default properties'] = ! empty($reflection->getDefaultProperties()) ? $reflection->getDefaultProperties() : 'Class has no default properties';

        if(null === $reflection->getConstructor())
        {
            $this->object['Class construct'] = 'Class has no construct.';
        }
        else
        {
            $this->object['Class construct'] = $reflection->getConstructor();
        }

        $this->object['Class interfaces'] = ! empty($reflection->getInterfaces()) ? $reflection->getInterfaces() : 'Class implements no interfaces';
        $this->object['Class traits'] = ! empty($reflection->getTraits()) ? $reflection->getTraits() : 'Class has no traits';
        $this->object['Class parent'] = ($reflection->getParentClass()) !== false ? $reflection->getParentClass() : 'Class has no parent';

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
        $this->html .= "<dd class=\"" . $this->cssClasses['dd'] . "\">";
        $this->html .= "<dl class=\"" . $this->cssClasses['dl'] . "\">";
        foreach($array as $key => $value)
        {
            if(Types::getType($value) === Types::TYPE_ARRAY)
            {
                $printKey = $this->escape($key);
                $this->html .=
                    "<dt class=\"" . $this->cssClasses['dt'] . "\">" .
                    "<span class=\"css-array-keys\"> {$printKey}</span>" .
                    "<span class=\"css-pointer\"> => </span>" .
                    "<br/>" .
                    "</dt>";
                $this->buildFromObjectInformationRecursive($value);
            }
            else
            {
                $printKey = $this->escape($key);
                $printValue = $this->escape($value);
                $this->html .=
                    "<dt class=\"" . $this->cssClasses['dt'] . "\">" .
                    "<span class=\"css-array-keys\"> {$printKey}</span>" .
                    "<span class=\"css-pointer\"> =></span> " .
                    "<span class=\"css-array-values\">{$printValue}</span>" .
                    "</dt>" .
                    "<dd class=\"" . $this->cssClasses['dd'] . "\">";
            }
        }
        $this->html .= "</dl>";
        $this->html .= "</dd>";
    }

    /**
     * @param array $options
     */
    private function buildHeader(array $options)
    {
        $this->html .=
            "<div class=\"php-pretty-print-header\">" .
                "<dl>" .
                    "<dt>Where was PhpPrettyPrint::dump() called?</dt>" .
                    "<dd>File: {$options['file']}</dd>" .
                    "<dd>Line: {$options['line']}</dd>" .
                "</dl>" .
            "</div>" .
            "<hr>";
    }

    /**
     * @param array $array
     * @param array $options
     */
    private function buildFromArray(array $array, array $options)
    {
        $this->buildFromArrayRecursive($array, $options);
    }

    /**
     * @param $array
     * @param $options
     *
     * @throws \Exception
     */
    private function buildFromArrayRecursive($array, array $options)
    {
        $this->html .= "<dd class=\"" . $this->cssClasses['dd'] . "\">";
        $this->html .= "<dl class=\"" . $this->cssClasses['dl'] . "\">";

        foreach($array as $key => $value)
        {
            if(is_callable($value) || is_object($value))
            {
                $this->html .= "<dt class=\"" . $this->cssClasses['dt'] . "\"><span class=\"css-string-object\">Object</span></dt>";
                $this->buildFromObject($value);
            }
            else
            {
                if(Types::getType($value) === Types::TYPE_ARRAY)
                {
                    $typeKey = gettype($key);
                    $typeValue = gettype($value);

                    $lengthValue = count($value);

                    $printKey = $this->getExcerpt($this->escape($key), $options);

                    $lengthKey = strlen($key);

                    if($typeKey == 'string')
                    {
                        $this->html .=
                            "<dt class=\"" . $this->cssClasses['dt'] . "\">" .
                            "<span class=\"css-array-keys\">{$typeKey} ({$lengthKey}) \"{$printKey}\"</span>" .
                            "<span class=\"css-pointer\"> => </span>" .
                            "<span class=\"css-array-values\">{$typeValue}({$lengthValue})</span>" .
                            "<br/>" .
                            "</dt>";
                    }
                    else
                    {
                        $this->html .=
                            "<dt class=\"" . $this->cssClasses['dt'] . "\">" .
                            "<span class=\"css-array-keys\">{$typeKey} {$printKey}</span>" .
                            "<span class=\"css-pointer\"> => </span>" .
                            "<span class=\"css-array-values\">{$typeValue}({$lengthValue})</span>" .
                            "<br/>" .
                            "</dt>";
                    }

                    $this->buildFromArrayRecursive($value, $options);
                }
                else
                {
                    $printKey = $this->escape($key);
                    $printValue = $this->getExcerpt($this->escape($value), $options);

                    $typeKey = gettype($key);
                    $typeValue = gettype($value);

                    $lengthValue = strlen($value);
                    $lengthKey = strlen($key);

                    if($typeKey == 'string')
                    {
                        if($typeValue == 'string')
                        {
                            $this->html .=
                                "<dt class=\"" . $this->cssClasses['dt'] . "\">" .
                                "<span class=\"css-array-keys\">{$typeKey} ({$lengthKey}) \"{$printKey}\"</span>" .
                                "<span class=\"css-pointer\"> => </span>" .
                                "<span class=\"css-array-values\">{$typeValue} ({$lengthValue})\"{$printValue}\"</span>" .
                                "</dt>" .
                                "<dd class=\"" . $this->cssClasses['dd'] . "\"></dd>";
                        }
                        else
                        {
                            $this->html .=
                                "<dt class=\"" . $this->cssClasses['dt'] . "\">" .
                                "<span class=\"css-array-keys\">{$typeKey} ({$lengthKey}) \"{$printKey}\"</span>" .
                                "<span class=\"css-pointer\"> => </span>" .
                                "<span class=\"css-array-values\">{$typeValue} {$printValue}</span>" .
                                "</dt>" .
                                "<dd class=\"" . $this->cssClasses['dd'] . "\"></dd>";
                        }
                    }
                    else
                    {
                        if($typeValue == 'string')
                        {
                            $this->html .=
                                "<dt class=\"" . $this->cssClasses['dt'] . "\">" .
                                "<span class=\"css-array-keys\">{$typeKey} {$printKey}</span>" .
                                "<span class=\"css-pointer\"> => </span>" .
                                "<span class=\"css-array-values\">{$typeValue} ({$lengthValue})\"{$printValue}\"</span>" .
                                "</dt>" .
                                "<dd class=\"" . $this->cssClasses['dd'] . "\"></dd>";
                        }
                        else
                        {
                            $this->html .=
                                "<dt class=\"" . $this->cssClasses['dt'] . "\">
                                <span class=\"css-array-keys\">{$typeKey} {$printKey}</span>" .
                                "<span class=\"css-pointer\"> => </span>" .
                                "<span class=\"css-array-values\">{$typeValue} {$printValue}</span>" .
                                "</dt>" .
                                "<dd class=\"" . $this->cssClasses['dd'] . "\"></dd>";
                        }
                    }
                }
            }
        }
        $this->html .= "</dl>";
        $this->html .= "</dd>";
    }
}
