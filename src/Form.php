<?php
namespace SeanKndy\SMVC;

use Psr\Http\Message\ServerRequestInterface;

/*
 * Basic helper class to simplify building HTML form inputs.
 *
 */

class Form
{
    private $request;
    private $view;

    public function __construct(View $v, ServerRequestInterface $req) {
        $this->request = $req;
        $this->view = $v;
    }
    
    public function create($name, $method = 'post', $class = '') {
        return '<form id="form_' . $name .'" method="' . $method . '" action="' . 
            $this->request->getUri()->getPath() . '"' . ($class ? ' class="' . $class . '"' : '') . '>';
    }
    
    public function text($name, array $ops = []) {
        $html = '<input type="' . (isset($ops['password']) ? 'password' : 'text') . '" name="' . $name . '" id="' . 
            $name . '" value="' . $this->getDefaultValue($name) . '"';
        if (isset($ops['size'])) $html .= ' size="' . $ops['size'] . '"';
        if (isset($ops['maxlength'])) $html .= ' maxlength="' . $ops['maxlength'] . '"';
        if (isset($ops['placeholder'])) $html .= ' placeholder="' . $ops['placeholder'] . '"';
        $html .= '>';
        return $html;
    }
    
    public function password($name, array $ops = []) {
        return $this->text($name, array_merge($ops, ['password' => true]));
    }

    public function textarea($name, array $ops = []) {
        $html = '<textarea name="' . $name . '" id="' . $name . '"';
        if (isset($ops['rows'])) $html .= ' rows="' . $ops['rows'] . '"';
        if (isset($ops['cols'])) $html .= ' cols="' . $ops['cols'] . '"';
        if (isset($ops['placeholder'])) $html .= ' placeholder="' . $ops['placeholder'] . '"';
        $html .= '>' . $this->getDefaultValue($name) . '</textarea>';
        return $html;
    }

    public function radio($name, $value, array $ops = []) {
        $html = '<input type="radio" name="' . $name . '" id="' . $name . '" value="' . $value . '"';
        if ($this->getDefaultValue($name) == $value)
            $html .= ' checked="checked"';
        $html .= '>';
        return $html;
    }

    public function checkbox($name, $value, array $ops = []) {
        $html = '<input type="checkbox" name="' . $name . '" id="' . $name . '" value="' . $value . '"';
        if ($this->getDefaultValue($name) == $value)
            $html .= ' checked="checked"';
        $html .= '>';
        return $html;
    }

    public function select($name, array $items = [], array $ops = []) {
        if (!$items) {
            $items = $this->findItemsInView($name);
        }
    
        $html = '<select name="' . $name . '" id="' . $name . '">';
        foreach ($items as $k => $v) {
            $val = isset($ops['use_keys']) ? $k : $v;
            $html .= '<option value="' . $val . '"';
            if ($this->getDefaultValue($name) == $val)
                $html .= ' selected="selected"';
            $html .= ">$v</option>";
        }
        $html .= '</select>';
        return $html;
    }
    
    public function submit($value, $name = null) {
        return '<input type="submit" ' . ($name ? 'name="' . $name . '"' : '') . 'value="' . $value . '">';
    }
    
    public function end($btnVal = null) {
        $html = '';
        if ($btnVal) {
            $html .= '<input type="submit" value="' . $btnVal . '">';
        }
        $html .= '</form>';
        return $html;
    }
    
    protected function findItemsInView($name) {
        // if name is something like 'category_id', then try to find the array 'categories' set in the View
        if (substr($name, -3) == '_id') {
            $name = substr($name, 0, -3);
            if (($pluralName = Utils\Inflect::pluralize($name)) != $name) {
                if (is_array($items = $this->view->get($pluralName))) {
                    return $items;
                }
            }
        } else if (($pluralName = Utils\Inflect::pluralize($name)) != $name) {
            if (is_array($items = $this->view->get($pluralName))) {
                return $items;
            }
        }
        return [];
    }
    
    protected function getDefaultValue($name) {
        $attributes = $this->request->getAttributes();
        if (isset($attributes[$name])) {
            return $attributes[$name];
        }
        if (isset($this->view->$name)) {
            return $this->view->get($name);
        }
    }
}
