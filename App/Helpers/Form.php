<?php
/**
 * Form builder class
 *
 * Inspired and picked from CodeIgniter
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @filesource
 */

namespace Waod\App\Helpers;
class Form
{
    const CHARSET = 'utf8';

    /**
     * Form Declaration
     *
     * Creates the opening portion of the form.
     *
     * @param string    the URI segments of the form destination
     * @param string    a key/value pair of action
     * @param array    a key/value pair of attributes
     * @param array    a key/value pair hidden data
     * @return    string
     */
    function open($url = '', $action = '', $attributes = array(), $hidden = array())
    {
        // If no action is provided then set to the current url
        if (empty($url)) {
            if (is_admin()) {
                $url = admin_url('admin.php');
            } else {
                $url = site_url();
            }
        }
        $attributes = $this->attributesToString($attributes);
        if (stripos($attributes, 'method=') === FALSE) {
            $attributes .= ' method="post"';
        }
        if (stripos($attributes, 'accept-charset=') === FALSE) {
            $attributes .= ' accept-charset="' . strtolower(self::CHARSET) . '"';
        }
        $form = '<form action="' . $url . '"' . $attributes . ">\n";
        if (is_array($hidden)) {
            foreach ($hidden as $name => $value) {
                $form .= '<input type="hidden" name="' . $name . '" value="' . strip_tags($value) . '" />' . "\n";
            }
        }
        // Adding the nonce field
        if (!empty($action)) {
            $form .= '<input type="hidden" name="action" value="' . strip_tags($action) . '" />' . "\n";
            $form .= wp_nonce_field($action, '_wpnonce');
        }
        return $form;
    }

    /**
     * Form Declaration - Multipart type
     *
     * Creates the opening portion of the form, but with "multipart/form-data".
     *
     * @param string    the URI segments of the form destination
     * @param string    a key/value pair of url
     * @param array    a key/value pair hidden data
     * @return    string
     */
    function openMultipart($url = '', $action = '', $attributes = array(), $hidden = array())
    {
        if (is_string($attributes)) {
            $attributes .= ' enctype="multipart/form-data"';
        } else {
            $attributes['enctype'] = 'multipart/form-data';
        }
        return $this->open($url, $action, $attributes, $hidden);
    }

    /**
     * Hidden Input Field
     *
     * Generates hidden fields. You can pass a simple key/value string or
     * an associative array with multiple values.
     *
     * @param mixed $name Field name
     * @param string $value Field value
     * @param bool $recursing
     * @return    string
     */
    function hidden($name, $value = '', $recursing = FALSE)
    {
        static $form;
        if ($recursing === FALSE) {
            $form = "\n";
        }
        if (is_array($name)) {
            foreach ($name as $key => $val) {
                $this->hidden($key, $val, TRUE);
            }
            return $form;
        }
        if (!is_array($value)) {
            $form .= '<input type="hidden" name="' . $name . '" value="' . strip_tags($value) . "\" />\n";
        } else {
            foreach ($value as $k => $v) {
                $k = is_int($k) ? '' : $k;
                $this->hidden($name . '[' . $k . ']', $v, TRUE);
            }
        }
        return $form;
    }

    /**
     * Text Input Field
     *
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function input($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'text',
            'name' => is_array($data) ? '' : $data,
            'value' => $value
        );
        return '<input ' . $this->parseFormAttributes($data, $defaults) . $this->attributesToString($extra) . " />\n";
    }

    /**
     * Password Field
     *
     * Identical to the input function but adds the "password" type
     *
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function password($data = '', $value = '', $extra = '')
    {
        is_array($data) OR $data = array('name' => $data);
        $data['type'] = 'password';
        return $this->input($data, $value, $extra);
    }

    /**
     * Upload Field
     *
     * Identical to the input function but adds the "file" type
     *
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function upload($data = '', $value = '', $extra = '')
    {
        $defaults = array('type' => 'file', 'name' => '');
        is_array($data) OR $data = array('name' => $data);
        $data['type'] = 'file';
        return '<input ' . $this->parseFormAttributes($data, $defaults) . $this->attributesToString($extra) . " />\n";
    }

    /**
     * Textarea field
     *
     * @param mixed $data
     * @param string $value
     * @param mixed $extra
     * @return    string
     */
    function textarea($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'name' => is_array($data) ? '' : $data,
            'cols' => '40',
            'rows' => '20'
        );
        if (!is_array($data) OR !isset($data['value'])) {
            $val = $value;
        } else {
            $val = $data['value'];
            unset($data['value']); // textareas don't use the value attribute
        }
        return '<textarea ' . $this->parseFormAttributes($data, $defaults) . $this->attributesToString($extra) . '>'
            . strip_tags($val)
            . "</textarea>\n";
    }

    /**
     * editor
     * @param string $data
     * @param string $value
     * @param string $extra
     * @return string
     */
    function editor($data, $value = '', $extra = '')
    {
        $name = is_array($data) ? 'default_editor' : $data;
        if (!is_array($data) OR !isset($data['value'])) {
            $val = $value;
        } else {
            $val = $data['value'];
            unset($data['value']); // textareas don't use the value attribute
        }
        ob_start();
        wp_editor($val, $name);
        return ob_get_clean();
    }

    /**
     * Multi-select menu
     *
     * @param string
     * @param array
     * @param mixed
     * @param mixed
     * @return    string
     */
    function multiselect($name = '', $options = array(), $selected = array(), $extra = '')
    {
        $extra = $this->attributesToString($extra);
        if (stripos($extra, 'multiple') === FALSE) {
            $extra .= ' multiple="multiple"';
        }
        return $this->dropdown($name, $options, $selected, $extra);
    }

    /**
     * Drop-down Menu
     *
     * @param mixed $data
     * @param mixed $options
     * @param mixed $selected
     * @param mixed $extra
     * @return    string
     */
    function dropdown($data = '', $options = array(), $selected = array(), $extra = '')
    {
        $defaults = array();
        if (is_array($data)) {
            if (isset($data['selected'])) {
                $selected = $data['selected'];
                unset($data['selected']); // select tags don't have a selected attribute
            }
            if (isset($data['options'])) {
                $options = $data['options'];
                unset($data['options']); // select tags don't use an options attribute
            }
        } else {
            $defaults = array('name' => $data);
        }
        is_array($selected) OR $selected = array($selected);
        is_array($options) OR $options = array($options);
        // If no selected state was submitted we will attempt to set it automatically
        if (empty($selected)) {
            if (is_array($data)) {
                if (isset($data['name'], $_POST[$data['name']])) {
                    $selected = array($_POST[$data['name']]);
                }
            } elseif (isset($_POST[$data])) {
                $selected = array($_POST[$data]);
            }
        }
        $extra = $this->attributesToString($extra);
        $multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';
        $form = '<select ' . rtrim($this->parseFormAttributes($data, $defaults)) . $extra . $multiple . ">\n";
        foreach ($options as $key => $val) {
            $key = (string)$key;
            if (is_array($val)) {
                if (empty($val)) {
                    continue;
                }
                $form .= '<optgroup label="' . $key . "\">\n";
                foreach ($val as $optgroup_key => $optgroup_val) {
                    $sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
                    $form .= '<option value="' . strip_tags($optgroup_key) . '"' . $sel . '>'
                        . (string)$optgroup_val . "</option>\n";
                }
                $form .= "</optgroup>\n";
            } else {
                $form .= '<option value="' . strip_tags($key) . '"'
                    . (in_array($key, $selected) ? ' selected="selected"' : '') . '>'
                    . (string)$val . "</option>\n";
            }
        }
        return $form . "</select>\n";
    }

    /**
     * Checkbox Field
     *
     * @param mixed
     * @param string
     * @param bool
     * @param mixed
     * @return    string
     */
    function checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
    {
        $defaults = array('type' => 'checkbox', 'name' => (!is_array($data) ? $data : ''), 'value' => $value);
        if (is_array($data) && array_key_exists('checked', $data)) {
            $checked = $data['checked'];
            if ($checked == FALSE) {
                unset($data['checked']);
            } else {
                $data['checked'] = 'checked';
            }
        }
        if ($checked == TRUE) {
            $defaults['checked'] = 'checked';
        } else {
            unset($defaults['checked']);
        }
        return '<input ' . $this->parseFormAttributes($data, $defaults) . $this->attributesToString($extra) . " />\n";
    }

    /**
     * Radio Button
     *
     * @param mixed
     * @param string
     * @param bool
     * @param mixed
     * @return    string
     */
    function radio($data = '', $value = '', $checked = FALSE, $extra = '')
    {
        is_array($data) OR $data = array('name' => $data);
        $data['type'] = 'radio';
        return $this->checkbox($data, $value, $checked, $extra);
    }

    /**
     * Submit Button
     *
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function submit($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'submit',
            'name' => is_array($data) ? '' : $data,
            'value' => $value
        );
        return '<input ' . $this->parseFormAttributes($data, $defaults) . $this->attributesToString($extra) . " />\n";
    }

    /**
     * Reset Button
     *
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function reset($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'reset',
            'name' => is_array($data) ? '' : $data,
            'value' => $value
        );
        return '<input ' . $this->parseFormAttributes($data, $defaults) . $this->attributesToString($extra) . " />\n";
    }

    /**
     * Form Button
     *
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function button($data = '', $content = '', $extra = '')
    {
        $defaults = array(
            'name' => is_array($data) ? '' : $data,
            'type' => 'button'
        );
        if (is_array($data) && isset($data['content'])) {
            $content = $data['content'];
            unset($data['content']); // content is not an attribute
        }
        return '<button ' . $this->parseFormAttributes($data, $defaults) . $this->attributesToString($extra) . '>'
            . $content
            . "</button>\n";
    }

    /**
     * Form Label Tag
     *
     * @param string    The text to appear onscreen
     * @param string    The id the label applies to
     * @param mixed    Additional attributes
     * @return    string
     */
    function label($label_text = '', $id = '', $attributes = array())
    {
        $label = '<label';
        if ($id !== '') {
            $label .= ' for="' . $id . '"';
        }
        $label .= $this->attributesToString($attributes);
        return $label . '>' . $label_text . '</label>';
    }

    /**
     * Fieldset Tag
     *
     * Used to produce <fieldset><legend>text</legend>.  To close fieldset
     * use form_fieldset_close()
     *
     * @param string    The legend text
     * @param array    Additional attributes
     * @return    string
     */
    function fieldset($legend_text = '', $attributes = array())
    {
        $fieldset = '<fieldset' . $this->attributesToString($attributes) . ">\n";
        if ($legend_text !== '') {
            return $fieldset . '<legend>' . $legend_text . "</legend>\n";
        }
        return $fieldset;
    }

    /**
     * Fieldset Close Tag
     *
     * @param string
     * @return    string
     */
    function fieldsetClose($extra = '')
    {
        return '</fieldset>' . $extra;
    }

    /**
     * Form Close Tag
     *
     * @param string
     * @return    string
     */
    function close($extra = '')
    {
        return '</form>' . $extra;
    }

    /**
     * Form Prep
     *
     * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
     *
     * @param string|string[] $str Value to escape
     * @return    string|string[]    Escaped values
     * @deprecated    3.0.0    An alias for strip_tags()
     */
    function prep($str)
    {
        return strip_tags($str, TRUE);
    }

    /**
     * Parse the form attributes
     *
     * Helper function used by some of the form helpers
     *
     * @param array $attributes List of attributes
     * @param array $default Default values
     * @return    string
     */
    private function parseFormAttributes($attributes, $default)
    {
        if (is_array($attributes)) {
            foreach ($default as $key => $val) {
                if (isset($attributes[$key])) {
                    $default[$key] = $attributes[$key];
                    unset($attributes[$key]);
                }
            }
            if (count($attributes) > 0) {
                $default = array_merge($default, $attributes);
            }
        }
        $att = '';
        foreach ($default as $key => $val) {
            if ($key === 'value') {
                $val = strip_tags($val);
            } elseif ($key === 'name' && !strlen($default['name'])) {
                continue;
            }
            $att .= $key . '="' . $val . '" ';
        }
        return $att;
    }

    /**
     * Attributes To String
     *
     * Helper function used by some of the form helpers
     *
     * @param mixed
     * @return    string
     */
    private function attributesToString($attributes)
    {
        if (empty($attributes)) {
            return '';
        }
        if (is_object($attributes)) {
            $attributes = (array)$attributes;
        }
        if (is_array($attributes)) {
            $atts = '';
            foreach ($attributes as $key => $val) {
                $atts .= ' ' . $key . '="' . $val . '"';
            }
            return $atts;
        }
        if (is_string($attributes)) {
            return ' ' . $attributes;
        }
        return FALSE;
    }
}