<?php

namespace Waod\App\Controllers;

class Configuration
{
    /**
     * settings constant
     * @var string
     */
    const CONFIG = 'waod-config';
    /**
     * Contains all the configuration details
     * @var array
     */
    private static $config = array(), $default_config = array(
        'coupon_message' => '<table cellpadding="0" cellspacing="0" width="100%" border="0"> <tbody> <tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;padding-top:0px;padding-bottom:0px;" align="center"> <div style="cursor:auto;color:#444;font-family:Trebuchet MS, Helvetica, sans-serif;font-size:32px;font-weight:200;line-height:28px;height:auto;text-align:center;"> <p>EXTRA {{coupon_value}} OFF</p> </div> </td> </tr> <tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="center"> <div style="cursor:auto;color:#8e9197;font-family:Helvetica, serif;font-size:16px;font-weight:100;line-height:1.6em;text-align:center;"> <p>Here is your Couponcode. </p> </div> </td> </tr> <tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;padding-top:20px;padding-bottom:20px;padding-right:20px;padding-left:20px;" align="center"> <div style="cursor:auto;color:#00D2C2;font-family:Trebuchet MS, Helvetica, sans-serif;font-size:20px;font-weight:600;line-height:22px;text-align:center;text-decoration:none;">{{coupon_code}} </div> </td> </tr> <tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="center"> <div style="cursor:auto;color:#8e9197;font-family:Helvetica, serif;font-size:16px;font-weight:100;line-height:1.6em;text-align:center;"> <p>Use this coupon to get {{coupon_value}} on your next order.</p> </div> </td> </tr> <tr> <td style="word-wrap:break-word;font-size:0px;padding:10px 25px;" align="center"> <table role="presentation" cellpadding="0" cellspacing="0" style="border-collapse:separate;" align="center" border="0"> <tbody> <tr> <td style="border:2px #00D2C2 ##00D2C2;border-radius:3px;color:#ffffff;cursor:auto;padding:10px 25px;" align="center" valign="middle" bgcolor="#00D2C2"> <a href="{{coupon_apply_url}}" style="text-decoration:none;background:#00D2C2;color:#ffffff;font-family:Trebuchet MS, Helvetica, sans-serif;font-size:16px;font-weight:normal;line-height:120%;text-transform:none;margin:0px;" target="_blank">Use Now</a> </td> </tr> </tbody> </table> </td> </tr> </tbody> </table> </div> </td> </tr> </tbody> </table>',
        'spin_message' => '<table border="0" width="100%" cellspacing="0" cellpadding="0"><tbody><tr><td style="word-wrap: break-word; font-size: 0px; padding: 10px 25px; padding-top: 0px; padding-bottom: 0px;" align="center"><div style="cursor: auto; color: #444; font-family: Trebuchet MS, Helvetica, sans-serif; font-size: 32px; font-weight: 200; line-height: 28px; height: auto; text-align: center;"><p>YOU HAVE WON {{spin_point}} POINT</p></div></td></tr><tr><td style="word-wrap: break-word; font-size: 0px; padding: 10px 25px;" align="center"><div style="cursor: auto; color: #8e9197; font-family: Helvetica, serif; font-size: 16px; font-weight: 100; line-height: 1.6em; text-align: center;"><p>use this point to win any lucky prizes</p></div></td></tr><tr><td style="word-wrap: break-word; font-size: 0px; padding: 10px 25px;" align="center"><div style="cursor: auto; color: #8e9197; font-family: Helvetica, serif; font-size: 16px; font-weight: 100; line-height: 1.6em; text-align: center;"><p><strong>Prize Details :</strong> {{spin_details}}</p></div></td></tr></tbody></table>'
    );

    /**
     * @param $key - what configuration need to get
     * @param string $default - default value if config value not found
     * @return string - configuration value
     */
    function getConfig($key, $default = '')
    {
        if (empty(self::$config)) {
            $this->setConfig();
        }
        if (isset(self::$config[$key])) {
            return self::$config[$key];
        } elseif (isset(self::$default_config[$key])) {
            //Check config found in default config
            return self::$default_config[$key];
        } else {
            return $default;
        }
    }

    /**
     * Set rule configuration to static variable
     */
    function setConfig()
    {
        $option = get_option(self::CONFIG);
        if ($option && !empty($option)) {
            if (is_string($option)) {
                $option = json_decode($option, true);
            }
            self::$config = $option;
        }
    }

    /**
     * Save the settings
     * @param $data
     * @return mixed
     */
    function save($data)
    {
        $settings = get_option(self::CONFIG);
        if (empty($settings)) {
            return add_option(self::CONFIG, $data);
        } else {
            return update_option(self::CONFIG, $data);
        }
    }
}