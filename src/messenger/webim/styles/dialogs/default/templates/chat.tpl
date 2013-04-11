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
            // Localized strings for the core
            Mibew.Localization.set({
                'chat.close.confirmation': ${msgjs:chat.close.confirmation},
                'typing.remote': ${msgjs:typing.remote},
                'chat.window.predefined.select_answer': ${msgjs:chat.window.predefined.select_answer},
                'chat.window.send_message': ${msgjs:chat.window.send_message},
                'chat.window.send_message_short_and_shortcut': ${msgjs:chat.window.send_message_short,send_shortcut},
                'chat.window.close_title': ${msgjs:chat.window.close_title},
                'chat.window.toolbar.refresh': ${msgjs:chat.window.toolbar.refresh},
                'chat.window.toolbar.mail_history': ${msgjs:chat.window.toolbar.mail_history},
                'chat.window.toolbar.redirect_user': ${msgjs:chat.window.toolbar.redirect_user},
                'page.analysis.userhistory.title': ${msgjs:page.analysis.userhistory.title},
                'chat.client.name': ${msgjs:chat.client.name},
                'chat.client.changename': ${msgjs:chat.client.changename},
                'chat.window.toolbar.turn_off_sound': ${msgjs:chat.window.toolbar.turn_off_sound},
                'chat.window.toolbar.turn_on_sound': ${msgjs:chat.window.toolbar.turn_on_sound},
                'chat.window.poweredby': ${msgjs:chat.window.poweredby},
                'chat.mailthread.sent.close': ${msgjs:chat.mailthread.sent.close},
                'form.field.department': ${msgjs:form.field.department},
                'form.field.department.description': ${msgjs:form.field.department.description},
                'form.field.email': ${msgjs:form.field.email},
                'form.field.name': ${msgjs:form.field.name},
                'form.field.message': ${msgjs:form.field.message},
                'leavemessage.close': ${msgjs:leavemessage.close},
                'leavemessage.descr': ${msgjs:leavemessage.descr},
                'leavemessage.sent.message': ${msgjs:leavemessage.sent.message},
                'leavemessage.error.email.required': ${pagejs:localized.email.required},
                'leavemessage.error.name.required': ${pagejs:localized.name.required},
                'leavemessage.error.message.required': ${pagejs:localized.message.required},
                'leavemessage.error.wrong.email': ${pagejs:localized.wrong.email},
                'errors.captcha': ${msgjs:errors.captcha},
                'mailthread.perform': ${msgjs:mailthread.perform},
                'presurvey.name': ${msgjs:presurvey.name},
                'presurvey.mail': ${msgjs:presurvey.mail},
                'presurvey.question': ${msgjs:presurvey.question},
                'presurvey.submit': ${msgjs:presurvey.submit},
                'presurvey.error.wrong_email': ${msgjs:presurvey.error.wrong_email},
                'presurvey.title': ${msgjs:presurvey.title},
                'presurvey.intro': ${msgjs:presurvey.intro}
            });
            // Plugins localization
            Mibew.Localization.set(${page:additional_localized_strings});
        //--></script>

        <!-- Run application -->
        <script type="text/javascript"><!--
            jQuery(document).ready(function(){
                Mibew.Application.start({
                    server: {
                        url: "${webimroot}/thread.php",
                        requestsFrequency: ${page:frequency}
                    },
                    page: {
                        style: '${styleid}',
                        webimRoot: '${webimroot}',
                        tplRoot: '${tplroot}',
                        company: {
                            name: ${pagejs:company.name},
                            chatLogoURL: '${page:company.chatLogoURL}'
                        },
                        webimHost: '${page:webimHost}',
                        title: ${pagejs:page.title}
                    },
                    ${if:chatOptions}
                        chatOptions: ${page:chatOptions},
                    ${endif:chatOptions}
                    ${if:surveyOptions}
                        surveyOptions: ${page:surveyOptions},
                    ${endif:surveyOptions}
                    ${if:leaveMessageOptions}
                        leaveMessageOptions: ${page:leaveMessageOptions},
                    ${endif:leaveMessageOptions}
                    startFrom: "${page:startFrom}",
                    plugins: ${page:js_plugin_options}
                });
            });
        //--></script>

    </head>
    <body>
        <div id="main-region"></div>
    </body>
</html>