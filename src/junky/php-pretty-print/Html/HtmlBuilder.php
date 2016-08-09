<?php

namespace Junky\PhpPrettyPrint\Html;
use Junky\PhpPrettyPrint\Types;

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
                "<dl class=\"css-parent\">" .
                    "<dt>" .
                    "<span class=\"css-argument-and-type\">{$options['argument_name']} ({$type})</span>" .
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
     * @param \ReflectionObject $reflection
     */
    private function buildFromObject($param, $reflection)
    {
        foreach($param as $key => $value)
        {
            $this->object['Object default'][$key] = $value;
        }

        // Get info on the object
        $this->object['Reflection']['In namespace'] = $reflection->inNamespace() ? 'Yes' : 'No';
        if($reflection->inNamespace())
        {
            $this->object['Class namespace'] = $reflection->getNamespaceName();
        }

        $this->object['Reflection']['Class name'] = $reflection->getName();

        $this->object['Reflection']['Is internal'] = $reflection->isInternal() ? 'Yes' : 'No';
        $this->object['Reflection']['Is iterable'] = $reflection->isIterateable() ? 'Yes' : 'No';
        $this->object['Reflection']['Is abstract'] = $reflection->isAbstract() ? 'Yes' : 'No';
        $this->object['Reflection']['Is final'] = $reflection->isFinal() ? 'Yes' : 'No';
        $this->object['Reflection']['Is user defined'] = $reflection->isUserDefined() ? 'Yes' : 'No';
        $this->object['Reflection']['Is instantiable'] = $reflection->isInstantiable() ? 'Yes' : 'No';
        $this->object['Reflection']['Is clonable'] = $reflection->isCloneable() ? 'Yes' : 'No';
        $this->object['Reflection']['Is interface'] = $reflection->isInterface() ? 'Yes' : 'No';
        $this->object['Reflection']['Class constants'] = ! empty($reflection->getConstants()) ? $reflection->getConstants() : 'Class has no constants';
        $this->object['Reflection']['Class static properties'] = ! empty($reflection->getStaticProperties()) ? $reflection->getStaticProperties() : 'Class has no static properties';
        $this->object['Reflection']['Class default properties'] = ! empty($reflection->getDefaultProperties()) ? $reflection->getDefaultProperties() : 'Class has no default properties';

        if(null === $reflection->getConstructor())
        {
            $this->object['Reflection']['Class construct'] = 'Class has no construct.';
        }
        else
        {
            $this->object['Reflection']['Class construct'] = $reflection->getConstructor();
        }

        $this->object['Reflection']['Class interfaces'] = ! empty($reflection->getInterfaces()) ? $reflection->getInterfaces() : 'Class implements no interfaces';
        $this->object['Reflection']['Class traits'] = ! empty($reflection->getTraits()) ? $reflection->getTraits() : 'Class has no traits';
        $this->object['Reflection']['Class parent'] = ($reflection->getParentClass()) !== false ? $reflection->getParentClass() : 'Class has no parent';

        if(false === $reflection->getFileName())
        {
            $this->object['Reflection']['Defined in'] = 'Class is internal, no definition to provide.';
        }
        else
        {
            $this->object['Reflection']['Defined in'] = $reflection->getFileName();
        }

        if(false === $reflection->getFileName())
        {
            $this->object['Reflection']['Start line'] = 'Class is internal, no start line to provide.';
        }
        else
        {
            $this->object['Reflection']['Start line'] = $reflection->getFileName();
        }

        if(false === $reflection->getEndLine())
        {
            $this->object['Reflection']['End line'] = 'Class is internal, no end line to provide.';
        }
        else
        {
            $this->object['Reflection']['End line'] = $reflection->getEndLine();
        }

        if(false === $reflection->getDocComment())
        {
            $this->object['Reflection']['Doc comments'] = 'No documents to provide.';
        }
        else
        {
            $this->object['Reflection']['Doc comments'] = $reflection->getDocComment();
        }

        // End get info
        $this->html .= "<span class=\"js-parent-object\">";

        if( ! empty($this->object['Object default']))
        {
            $this->html .= "<div class=\"js-object-default-tab \"><button class=\"button-reflection button\">Show reflection</button></div>";

            $this->html .= "<div class=\"js-object-default \">";
            $this->buildFromObjectIterationInformationRecursive($this->object['Object default']);
            $this->html .= "</div>";
        }

        if($param instanceof \Closure)
        {
            $this->html .= "<div class=\"js-object-default-tab \"><button class=\"button-reflection button\">Show reflection</button></div>";

            $this->html .= "<div class=\"js-object-default \">";
            $this->html .= "<span class=\"css-type-string\">Nothing here...</span>";
            $this->html .= "</div>";
        }

        $this->html .= "<div class=\"js-object-reflection-tab hide\"><button class=\"button-class-default button\">Show default</button></div>";

        $this->html .= "<div class=\"js-object-reflection hide\">";
        $this->buildFromObjectReflectionInformationRecursive($this->object['Reflection']);
        $this->html .= "</div>";
        $this->html .= "</span>";

        $this->object = [];
    }

    private function buildFromObjectIterationInformationRecursive($array)
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
                $this->buildFromObjectIterationInformationRecursive($value);
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
     * @param array $array
     *
     * @throws \Exception
     */
    private function buildFromObjectReflectionInformationRecursive($array)
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
                $this->buildFromObjectReflectionInformationRecursive($value);
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
            "<div class=\"css-header\">" .
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
            if(is_object($value))
            {
                $reflection = new \ReflectionObject($value);

                $this->html .= "<dt class=\"" . $this->cssClasses['dt'] . "\"><span class=\"css-string-object\">Object: {$reflection->getName()}</span></dt>";
                $this->buildFromObject($value, $reflection);
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
