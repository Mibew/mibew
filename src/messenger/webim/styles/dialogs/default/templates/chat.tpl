<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
        <title>${msg:chat.window.title.agent}</title>
        <link rel="shortcut icon" href="${webimroot}/images/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="${tplroot}/chat.css" media="all" />
        <!--[if IE 7]>
            <link rel="stylesheet" type="text/css" href="${tplroot}/chat_ie7.css" media="all" />
        <![endif]-->
        ${page:additional_css}

        <!-- External libs -->
        <script type="text/javascript" src="${webimroot}/js/libs/jquery.min.js"></script>
        <script type="text/javascript" src="${webimroot}/js/libs/json2.js"></script>
        <script type="text/javascript" src="${webimroot}/js/libs/underscore-min.js"></script>
        <script type="text/javascript" src="${webimroot}/js/libs/backbone-min.js"></script>
        <script type="text/javascript" src="${webimroot}/js/libs/backbone.marionette.min.js"></script>
        <script type="text/javascript" src="${webimroot}/js/libs/handlebars.js"></script>

        <!-- Javascript templates -->
        <script type="text/javascript" src="${tplroot}/js/compiled/templates.js"></script>

        <!-- Application files -->
        <script type="text/javascript" src="${webimroot}/js/compiled/mibewapi.js"></script>
        <script type="text/javascript" src="${webimroot}/js/compiled/default_app.js"></script>
        <script type="text/javascript" src="${webimroot}/js/compiled/chat_app.js"></script>

        <!-- Add style scripts -->
        <script type="text/javascript" src="${tplroot}/js/compiled/scripts.js"></script>

        ${page:additional_js}
        <script type="text/javascript"><!--
            Mibew.Localization.set({
                'chat.close.confirmation': "${msg:chat.close.confirmation}",
                'typing.remote': "${msg:typing.remote}",
                'chat.window.predefined.select_answer': "${msg:chat.window.predefined.select_answer}",
                'chat.window.send_message': "${msg:chat.window.send_message}",
                'chat.window.send_message_short_and_shortcut': "${msg:chat.window.send_message_short,send_shortcut}",
                'chat.window.close_title': "${msg:chat.window.close_title}",
                'chat.window.toolbar.refresh': "${msg:chat.window.toolbar.refresh}",
                'chat.window.toolbar.mail_history': "${msg:chat.window.toolbar.mail_history}",
                'chat.window.toolbar.redirect_user': "${msg:chat.window.toolbar.redirect_user}",
                'page.analysis.userhistory.title': "${msg:page.analysis.userhistory.title}",
                'chat.client.name': "${msg:chat.client.name}",
                'chat.client.changename': "${msg:chat.client.changename}",
                'chat.window.toolbar.turn_off_sound': "${msg:chat.window.toolbar.turn_off_sound}",
                'chat.window.toolbar.turn_on_sound': "${msg:chat.window.toolbar.turn_on_sound}",
                'chat.window.poweredby': "${msg:chat.window.poweredby}"
            });
        //--></script>

        <!-- Run application -->
        <script type="text/javascript"><!--
            jQuery(document).ready(function(){
                Mibew.Application.start({
                    user: {
                        ${if:user}
                        name: "${page:ct.user.name}",
                        canChangeName: ${if:canChangeName}true${else:canChangeName}false${endif:canChangeName},
                        defaultName: ("${page:ct.user.name}" == "${msg:chat.default.username}"),
                        ${endif:user}
                        canPost: ${if:canpost}true${else:canpost}false${endif:canpost},
                        isAgent: ${if:agent}true${else:agent}false${endif:agent}
                    },
                    server: {
                        url: "${webimroot}/thread.php",
                        requestsFrequency: ${page:frequency}
                    },
                    thread: {
                        id:${page:ct.chatThreadId},
                        token:${page:ct.token}
                    },
                    messageForm: {
                        ${if:agent}${if:canpost}
                        predefinedAnswers: ${page:predefinedAnswers},
                        ${endif:canpost}${endif:agent}
                        ignoreCtrl:${if:ignorectrl}true${else:ignorectrl}false${endif:ignorectrl}
                    },
                    links: {
                        mailLink: "${page:mailLink}",
                        redirectLink: "${page:redirectLink}",
                        historyLink: "${page:historyParamsLink}",
                        sslLink: "${page:sslLink}"
                    },
                    page: {
                        style: '${styleid}',
                        webimRoot: '${webimroot}',
                        tplRoot: '${tplroot}',
                        chatWindowParams: "${page:chatStyles.chatWindowParams}",
                        mailWindowParams: "${page:chatStyles.mailWindowParams}",
                        historyWindowParams: "${page:coreStyles.historyWindowParams}"
                    },
                    layoutsData: {
                        chat: {
                            user: ${if:user}true${else:user}false${endif:user}
                        }
                    },
                    plugins: ${page:js_plugin_options}
                });
            });
        //--></script>

    </head>
    <body>

        <!-- Chat window top. Includes logo and some info about company -->
        <div id="top">
            <div id="logo">
                ${if:ct.company.chatLogoURL}
                    ${if:webimHost}
                        <a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
                            <img src="${page:ct.company.chatLogoURL}" alt=""/>
                        </a>
                    ${else:webimHost}
                        <img src="${page:ct.company.chatLogoURL}" alt=""/>
                    ${endif:webimHost}
                ${else:ct.company.chatLogoURL}
                    ${if:webimHost}
                        <a onclick="window.open('${page:webimHost}');return false;" href="${page:webimHost}">
                            <img src="${tplroot}/images/default-logo.gif" alt=""/>
                        </a>
                    ${else:webimHost}
                        <img src="${tplroot}/images/default-logo.gif" alt=""/>
                    ${endif:webimHost}
                ${endif:ct.company.chatLogoURL}
                &nbsp;
                <div id="page-title">${page:chat.title}</div>
                <div class="clear">&nbsp;</div>
            </div>
        </div>

        <div id="main-region"></div>
    </body>
</html>