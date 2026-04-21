<?php

namespace App\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_METHOD)]
class PublicAPI extends AbstractAnnotation
{
    public function __construct()
    {
    }
}
