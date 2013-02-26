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
                'chat.window.poweredby': "${msg:chat.window.poweredby}",
                'chat.mailthread.sent.close': "${msg:chat.mailthread.sent.close}",
                'form.field.department': "${msg:form.field.department}",
                'form.field.department.description': "${msg:form.field.department.description}",
                'form.field.email': "${msg:form.field.email}",
                'form.field.name': "${msg:form.field.name}",
                'form.field.message': "${msg:form.field.message}",
                'leavemessage.close': "${msg:leavemessage.close}",
                'leavemessage.descr': "${msg:leavemessage.descr}",
                'leavemessage.sent.message': "${msg:leavemessage.sent.message}",
                'leavemessage.error.email.required': '${page:localized.email.required}',
                'leavemessage.error.name.required': '${page:localized.name.required}',
                'leavemessage.error.message.required': '${page:localized.message.required}',
                'leavemessage.error.wrong.email': '${page:localized.wrong.email}',
                'errors.captcha': '${msg:errors.captcha}',
                'mailthread.perform': "${msg:mailthread.perform}",
                'presurvey.name': "${msg:presurvey.name}",
                'presurvey.mail': "${msg:presurvey.mail}",
                'presurvey.question': "${msg:presurvey.question}",
                'presurvey.submit': "${msg:presurvey.submit}",
                'presurvey.error.wrong_email': "${msg:presurvey.error.wrong_email}",
                'presurvey.title': "${msg:presurvey.title}",
                'presurvey.intro': '${msg:presurvey.intro}'
            });
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
                            name: '${page:company.name}',
                            chatLogoURL: '${page:company.chatLogoURL}'
                        },
                        webimHost: '${page:webimHost}',
                        title: '${page:page.title}'
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