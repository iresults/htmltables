<?php
namespace Zarth\Htmltables\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class TableTagViewHelper extends AbstractTagBasedViewHelper
{
    protected $tagName = 'td';

    protected $validAttributes = ['title','rowspan','colspan','scope','abbr','class','headers','id'];

    public function initialize(): void
    {
        // we want to dynamically set the tag name by the
        // given argument, so we need to reset the default tag
        $tagName = $this->arguments['tagName'];
        if ($tagName!==$this->tagName) {
            $this->tag->reset();
            $this->tag = new TagBuilder($tagName);
            $this->tag->setTagName($tagName);
        }
    }
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('attributesInArray', 'array', 'Array of tag attributes');
        $this->registerArgument('role', 'string', 'Role attribute');
        $this->registerArgument('tagName', 'string', 'Name of tag');
    }

    public function render(): string
    {
        // set content
        $this->tag->setContent($this->renderChildren());
        
        // set role attribute
        if (!empty($this->arguments['role'])) {
            $role = $this->arguments['role'];
            $this->tag->addAttribute(
                'role', $role
            );
        }

        // set attributes
        $attributes = $this->arguments['attributesInArray'];
        array_walk($attributes, function($value,$key){
            if (!empty($value) && in_array($key, $this->validAttributes)) {
                $this->tag->addAttribute(
                    $key, $value
                );
            }
        });

        return $this->tag->render();
    }
}
