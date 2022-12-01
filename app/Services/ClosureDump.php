<?php

namespace App\Services;

use Closure;
use ReflectionFunction;

class ClosureDump
{
    public static function dump(Closure $closure): string {
        $str = 'function (';
        $r = new ReflectionFunction($closure);
        $params = array();
        foreach($r->getParameters() as $p) {
            $s = '';
            if($p->getType() === 'array') {
                $s .= 'array ';
            } else if($p->getType()) {
                $s .= (string) $p->getType() . ' ';
            }
            if($p->isPassedByReference()){
                $s .= '&';
            }
            $s .= '$' . $p->name;
            if($p->isOptional()) {
                $s .= ' = ' . var_export($p->getDefaultValue(), TRUE);
            }
            $params []= $s;
        }
        $str .= implode(', ', $params);
        $str .= '){' . PHP_EOL;
        $lines = file($r->getFileName());
        for($l = $r->getStartLine(); $l < $r->getEndLine(); $l++) {
            $str .= $lines[$l];
        }

        return rtrim(str_replace(["

    return view('result', ['result' => \$result]);",
            "function (){",
            "});",
        ], '', $str));
    }
}
