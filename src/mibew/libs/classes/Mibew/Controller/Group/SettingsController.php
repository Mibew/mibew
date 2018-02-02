<?php
/*
 * This file is a part of Mibew Messenger.
 *
 * Copyright 2005-2018 the original author or authors.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mibew\Controller\Group;

use Mibew\Http\Exception\NotFoundException;
use Mibew\Mail\Utils as MailUtils;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contains all actions which are related with operator group's settings.
 */
class SettingsController extends AbstractController
{
    /**
     * Builds a page with form for add/edit group.
     *
     * @param Request $request Incoming request.
     * @return string Rendered page content.
     * @throws NotFoundException If the operator's group with specified ID is
     *   not found in the system.
     */
    public function showFormAction(Request $request)
    {
        $operator = $this->getOperator();
        $group_id = $request->attributes->getInt('group_id');

        $page = array(
            'gid' => false,
            // Use errors list stored in the request. We need to do so to have
            // an ability to pass the request from the "submitEditForm" action.
            'errors' => $request->attributes->get('errors', array()),
        );

        if ($group_id) {
            // Check if the group exisits
            $group = group_by_id($group_id);
            if (!$group) {
                throw new NotFoundException('The group is not found.');
            }

            // Set form values
            $page['formname'] = $group['vclocalname'];
            $page['formdescription'] = $group['vclocaldescription'];
            $page['formcommonname'] = $group['vccommonname'];
            $page['formcommondescription'] = $group['vccommondescription'];
            $page['formemail'] = $group['vcemail'];
            $page['formweight'] = $group['iweight'];
            $page['formparentgroup'] = $group['parent'];
            $page['grid'] = $group['groupid'];
            $page['formtitle'] = $group['vctitle'];
            $page['formchattitle'] = $group['vcchattitle'];
            $page['formhosturl'] = $group['vchosturl'];
            $page['formlogo'] = $group['vclogo'];
        }

        // Override group's fields from the request if it's needed. This
        // case will take place when save handler fails.
        if ($request->isMethod('POST')) {
            $page['formname'] = $request->request->get('name');
            $page['formdescription'] = $request->request->get('description');
            $page['formcommonname'] = $request->request->get('commonname');
            $page['formcommondescription'] = $request->request->get('commondescription');
            $page['formemail'] = $request->request->get('email');
            $page['formweight'] = $request->request->get('weight');
            $page['formparentgroup'] = $request->request->get('parentgroup');
            $page['formtitle'] = $request->request->get('title');
            $page['formchattitle'] = $request->request->get('chattitle');
            $page['formhosturl'] = $request->request->get('hosturl');
            $page['formlogo'] = $request->request->get('logo');
        }

        // Set other page variables and render the template.
        $page['stored'] = $request->query->has('stored');
        $page['availableParentGroups'] = get_available_parent_groups($group_id);
        $page['formaction'] = $request->getBaseUrl() . $request->getPathInfo();
        $page['title'] = getlocal('Group details');
        $page['menuid'] = 'groups';
        $page = array_merge($page, prepare_menu($operator));
        $page['tabs'] = $this->buildTabs($request);

        $this->getAssetManager()->attachJs('js/compiled/group.js');

        return $this->render('group_edit', $page);
    }

    /**
     * Processes submitting of the form which is generated in
     * {@link \Mibew\Controller\GroupController::showEditFormAction()} method.
     *
     * @param Request $request incoming request.
     * @return string Rendered page content.
     */
    public function submitFormAction(Request $request)
    {
        csrf_check_token($request);

        $errors = array();

        $group_id = $request->attributes->get('group_id', false);

        $parent_group = $request->request->get('parentgroup');
        if (!$parent_group || !preg_match("/^\d{1,10}$/", $parent_group)) {
            $parent_group = null;
        }

        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $common_name = $request->request->get('commonname');
        $common_description = $request->request->get('commondescription');
        $email = $request->request->get('email');
        $weight = $request->request->get('weight');
        $title = $request->request->get('title');
        $chat_title = $request->request->get('chattitle');
        $host_url = $request->request->get('hosturl');
        $logo = $request->request->get('logo');

        if (!$name) {
            $errors[] = no_field("Name");
        }

        if ($email != '' && !MailUtils::isValidAddress($email)) {
            $errors[] = wrong_field("E-mail");
        }

        if (!preg_match("/^(\d{1,10})?$/", $weight)) {
            $errors[] = wrong_field("Weight");
        }

        if (!$weight) {
            $weight = 0;
        }

        $existing_group = group_by_name($name);
        $duplicate_name = (!$group_id && $existing_group)
            || ($group_id
                && $existing_group
                && $group_id != $existing_group['groupid']);

        if ($duplicate_name) {
            $errors[] = getlocal("Please choose another name because a group with that name already exists.");
        }

        if (count($errors) != 0) {
            $request->attributes->set('errors', $errors);

            // The form should be rebuild. Invoke appropriate action.
            return $this->showFormAction($request);
        }

        if (!$group_id) {
            // Greate new group
            $new_dep = create_group(array(
                'vclocalname' => $name,
                'vclocaldescription' => $description,
                'vccommonname' => $common_name,
                'vccommondescription' => $common_description,
                'vcemail' => $email,
                'iweight' => $weight,
                'parent' => $parent_group,
                'vctitle' => $title,
                'vcchattitle' => $chat_title,
                'vchosturl' => $host_url,
                'vclogo' => $logo));

            // Redirect an operator to group's member page.
            $redirect_to = $this->generateUrl(
                'group_members',
                array('group_id' => (int)$new_dep['groupid'])
            );
        } else {
            // Update exisitng group
            update_group(array(
                'groupid' => $group_id,
                'vclocalname' => $name,
                'vclocaldescription' => $description,
                'vccommonname' => $common_name,
                'vccommondescription' => $common_description,
                'vcemail' => $email,
                'iweight' => $weight,
                'parent' => $parent_group,
                'vctitle' => $title,
                'vcchattitle' => $chat_title,
                'vchosturl' => $host_url,
                'vclogo' => $logo));

            // Redirect an operator to group's page.
            $redirect_to = $this->generateUrl(
                'group_edit',
                array('group_id' => $group_id)
            );
        }

        return $this->redirect($redirect_to);
    }
}
