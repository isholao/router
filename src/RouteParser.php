<?php

namespace Isholao\Router;

/**
 * @author Ishola O <ishola.tolu@outlook.com>
 */
class RouteParser
{

    protected $template;

    public function __construct(string $template = '')
    {
        $this->template = $template;
    }

    /**
     * Set template
     * 
     * @param string $template
     * @throws \InvalidArgumentException
     */
    public function setTemplate(string $template)
    {
        if (empty($template))
        {
            throw new \InvalidArgumentException('Template cannot be empty.');
        }
        $this->template = $template;
    }

    /**
     * Get template
     * 
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * contains regex pattern
     * 
     * @param string $template
     * @return bool
     */
    public static function containsRegexPattern(string $template): bool
    {
        return (bool) \preg_match('#[{}]#', $template);
    }

    /**
     * Parse template against regexes
     * 
     * @param array $regexes
     * @return array
     */
    public function parse(array $regexes): array
    {
        $p = '';
        $r = [];
        $r['segments'] = [];

        $templateSegments = \preg_split('#\/#', $this->template, -1,
                                        \PREG_SPLIT_NO_EMPTY);
        foreach ($templateSegments as $segment)
        {
            $opt = FALSE;
            if (\preg_match('#[{}]#', $segment))
            {

                while (($start = \strpos($segment, '{')) !== FALSE)
                {
                    $end = \strpos($segment, '}');
                    $tmp = \substr($segment, $start, ($end + 1) - $start);

                    $parts = \explode('=', \str_replace(['{', '}'], '', $tmp));

                    $name = \trim($parts[0]);
                    $regex = \trim($parts[1]);

                    $pattern = "(?<$name>$regex)";
                    //set template params
                    $r['segments'][$name] = $regex;
                    //replace pattern in template segment
                    $segment = \str_replace($tmp, $pattern, $segment);
                }

                if ($segment[-1] == '?')
                {
                    $opt = TRUE;
                    $segment = \substr($segment, 0, (\strlen($segment) - 1));
                }

                if (!empty($regexes))
                {
                    $segment = \str_replace(\array_keys($regexes),
                                                        \array_values($regexes),
                                                                      $segment);
                }
            }

            if ($opt)
            {
                if (empty($p))
                {
                    $p = "/$segment?";
                } else
                {
                    $p .= "(/$segment)?";
                }
            } else
            {
                $p .= "/$segment";
            }
        }
        $r['regex'] = $p;
        return $r;
    }

}
