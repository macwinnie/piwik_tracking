<?php

/**
 * 
 * piwik_tracking
 *
 * Bind piwik analytics script - based on: http://github.com/igloonet/roundcube_google_analytics
 *
 * @version 1.0 - 01.05.2014
 * @author Martin Winter
 *
 **/

class piwik_tracking extends rcube_plugin {

    function init () {
        $this->load_config('config/config.inc.php');
        $this->add_hook('render_page', array($this, 'add_script'));
    }

    function add_script ($args) {
        $rcmail = rcmail::get_instance();
        $exclude = array_flip($rcmail->config->get('piwik_analytics_exclude'));
        
        if (isset($exclude[$args['template']]))
            return $args;
        if ($rcmail->config->get('piwik_tracking_privacy') and !empty($_SESSION['user_id']))
            return $args;

        if(!$rcmail->config->get('piwik_tracking_domain'))
            return $args;
    
        $script = '
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push([\'trackPageView\']);
  _paq.push([\'enableLinkTracking\']);
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://' . $rcmail->config->get('piwik_tracking_domain') . '/";
    _paq.push([\'setTrackerUrl\', u+\'piwik.php\']);
    _paq.push([\'setSiteId\', ' . $rcmail->config->get('piwik_tracking_id') . ']);
    var d=document, g=d.createElement(\'script\'), s=d.getElementsByTagName(\'script\')[0]; g.type=\'text/javascript\';
    g.defer=true; g.async=true; g.src=u+\'piwik.js\'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="http://' . $rcmail->config->get('piwik_analytics_domain') . '/piwik.php?idsite=' . $rcmail->config->get('piwik_tracking_id') . '" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
';
        // add script to end of page
        $rcmail->output->add_footer($script);
        return $args;
    }
}

?>
