<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * 51LA 网站统计 V6 for Typecho 插件
 *
 * @package 51LA 网站统计 V6
 * @author 51LA
 * @version 1.0.0
 * @license GNU General Public License 2.0
 * @link https://v6.51.la/
 */
class LaAnalysis_Plugin implements Typecho_Plugin_Interface
{
    public static $tableName = 'la_analysis';
    private static $pluginName = 'LaAnalysis';
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     */
    public static function activate()
    {
        $res = LaAnalysis_Plugin::install();
        Typecho_Plugin::factory('Widget_Archive')->header = array('LaAnalysis_Plugin', 'installSDK');
        return _t($res);
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     */
    public static function deactivate()
    {
    }

    /**
     * 获取插件配置面板
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $name = self::$tableName;
        $maskId = new Typecho_Widget_Helper_Form_Element_Text("maskId", null, '', _t('应用 MaskId'), '<span>如何获取应用 MaskId ？</span><br /><span>1. 前往 <a href="https://v6.51.la/?cc=YWEGU5" target="_blank">51LA网站统计(v6)</a> 注册账号，创建您的网站应用。（如已有账号忽略该步骤）</span><br><span>2. 前往 <a href="https://v6.51.la/user/application" target="_blank">站点管理页</a> 复制统计 掩码 MaskId：</span><br /><img width="534" src="/usr/plugins/LaAnalysis/get-mask-id.png" />');
        $type = new Typecho_Widget_Helper_Form_Element_Radio(
            "type",
            array(
                '0' => '同步引入',
                '1' => '异步引入',
            ),
            '0',
            _t('引入类型'),
            '* 同步引入：默认安装方式。<br>* 异步引入：异步引入方式下，统计代码相对于其他脚本，会延迟异步加载，代码加载不阻塞页面的解析。页面内容加载的时间存在优先于统计代码加载的情况，可能会导致统计数据因访客网络加载慢等问题无法执行到加载统计代码这一步，导致统计数据小于实际情况。若对页面性能的要求非常高，建议使用此方式。<br /><img src="//ia.51.la/go1?id=21273525&pvFlag=1" style="border:none;height:1px;width:1px;" />'
        );
        $form->addInput($maskId);
        $form->addInput($type);
    }

    /**
     * 自定义插件配置保存方法
     *
     * @param $config array 插件配置
	 * @param $is_init bool 是否初始化
     */
    public static function configHandle($config, $is_init) {
        Helper::configPlugin(self::$pluginName, $config);
    }

    /**
     * 个人用户的配置面板
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    /**
     * 插件实现方法
     *
     * @access public
     * @return void
     */
    public static function install()
    {
        $configLink = '<a href="' . Helper::options()->adminUrl . 'options-plugin.php?config=LaAnalysis">' . _t('设置插件') . '</a>';
        $msg = _t('插件启用成功!') . $configLink;
        return $msg;
    }

    public static function installSDK()
    {
        $name = self::$tableName;
        $pluginOption = Typecho_Widget::widget('Widget_Options')->Plugin('LaAnalysis');
        $pluginOption = unserialize($pluginOption);
        $maskId = $pluginOption["maskId"];
        $type = $pluginOption["type"];
        if ($type) {
            echo "<script>
            !function(p){'use strict';!function(t){var s=window,e=document,i=p,c=''.concat('https:'===e.location.protocol?'https://':'http://','sdk.51.la/js-sdk-pro.min.js'),n=e.createElement('script'),r=e.getElementsByTagName('script')[0];n.type='text/javascript',n.setAttribute('charset','UTF-8'),n.async=!0,n.src=c,n.id='LA_COLLECT',i.d=n;var o=function(){s.LA.ids.push(i)};s.LA?s.LA.ids&&o():(s.LA=p,s.LA.ids=[],o()),r.parentNode.insertBefore(n,r)}()}({id:'$maskId',ck:'$maskId'});
            </script>";
        } else {
            echo "<script charset='UTF-8' id='LA_COLLECT' src='//sdk.51.la/js-sdk-pro.min.js'></script>
                <script>LA.init({id: '$maskId',ck: '$maskId'})</script>";
        }
    }
}
