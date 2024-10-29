<style>#aklamatorTwitchCategory {
        min-width: 250px
    }</style>
<div id="aklamatorTwitch-options" style="width:1160px;margin-top:10px;">
    <div class="left" style="float: left;">
        <div style="float: left; width: 300px;">

            <a target="_blank" href="<?php echo $this->aklamator_url; ?>?utm_source=wp-plugin">
                <img style="border-radius:5px;border:0px;"
                     src=" <?php echo AKLATWITCH_PR_PLUGIN_URL . 'images/logo.png'; ?>"/></a>
            <?php
            if ($this->application_id != '') : ?>
                <a target="_blank" href="<?php echo $this->aklamator_url; ?>dashboard?utm_source=wp-plugin">
                    <img style="border:0px;margin-top:5px;border-radius:5px;"
                         src="<?php echo AKLATWITCH_PR_PLUGIN_URL . 'images/dashboard.jpg'; ?>"/></a>

            <?php endif; ?>

            <a target="_blank" href="<?php echo $this->aklamator_url; ?>contact?utm_source=wp-plugin-contact">
                <img style="border:0px;margin-top:5px; margin-bottom:5px;border-radius:5px;"
                     src="<?php echo AKLATWITCH_PR_PLUGIN_URL . 'images/support.jpg'; ?>"/></a>

            <a target="_blank" href="http://qr.rs/q/4649f"><img
                    style="border:0px;margin-top:5px; margin-bottom:5px;border-radius:5px;"
                    src="<?php echo AKLATWITCH_PR_PLUGIN_URL . 'images/promo-300x200.png'; ?>"/></a>

        </div>

        <div class="box">

            <h1>Aklamator Twitch Plugin</h1>

            <?php

            if (isset($this->api_data->error) || $this->application_id == '') : ?>
                <h3 style="float: left">Step 1: Get your Aklamator Aplication ID</h3>
                <a class='aklamator_button aklamator-login-button' id="aklamator_login_button" >Click here for FREE registration/login</a>
                <div style="clear: both"></div>
                <p>Or you can manually <a href="<?php echo $this->aklamator_url . 'registration/publisher'; ?>" target="_blank">register</a> or <a href="<?php echo $this->aklamator_url . 'login'; ?>" target="_blank">login</a> and copy paste your Application ID</p>
                <script>var signup_url = '<?php echo $this->getSignupUrl(); ?>';</script>
            <?php endif; ?>
            
            <div style="clear:both"></div>

                <div style="clear: both"></div>
                <?php if ($this->application_id == '') { ?>
                    <h3>Step 2: &nbsp;&nbsp;&nbsp;&nbsp; Paste your Aklamator Application ID</h3>
                <?php } else { ?>
                    <h3>Your Aklamator Application ID</h3>
                <?php } ?>


                <form method="post" action="options.php">
                    <?php
                    settings_fields('aklamatorTwitch-options');
                    ?>

                    <p>
                        <input type="text" style="width: 400px" name="aklamatorTwitchApplicationID"
                               id="aklamatorTwitchApplicationID" value="<?php
                        echo(get_option("aklamatorTwitchApplicationID"));
                        ?>" maxlength="999" onchange="appIDChange(this.value)"/>

                    </p>
                    <h3>Twich username</h3>
                    <p style="margin: 5px 0">
                        <input type="text" style="width: 400px" name="aklamatorTwitchChannelName"
                               id="aklamatorTwitchChannelName" value="<?php
                        echo(get_option("aklamatorTwitchChannelName"));
                        ?>" maxlength="999" />

                    </p>
                    <p style="margin: 5px 0">or</p>
                    <h3 style="margin: 5px 0">Game name</h3>
                    <p style="margin: 5px 0">
                        <input type="text" style="width: 400px" name="aklamatorTwitchGameName"
                               id="aklamatorTwitchGameName" value="<?php
                        echo(get_option("aklamatorTwitchGameName"));
                        ?>" maxlength="999" />

                    </p>
                    <p>
                        <input type="checkbox" id="aklamatorTwitchPoweredBy"
                               name="aklamatorTwitchPoweredBy" <?php echo(get_option("aklamatorTwitchPoweredBy") == true ? 'checked="checked"' : ''); ?>
                               Required="Required">
                        <strong>Required</strong> I acknowledge there is a 'powered by aklamator' link on the widget.
                        <br/>
                    </p>

                    <p>
                    <div class="alert alert-msg">
                        <strong>Note </strong><span style="color: red">*</span>: By default, posts without images will
                        not be shown in widgets. If you want to show them click on <strong>EDIT</strong> in table below!
                    </div>
                    </p>
                    <?php if(isset($this->api_data->flag) && $this->api_data->flag === false): ?>
                        <p id="aklamator_error" class="alert_red alert-msg_red"><span style="color:red"><?php echo $this->api_data->error; ?></span></p>
                    <?php endif; ?>

                    <?php if ($this->application_id !== '' && $this->api_data->flag): ?>

                        <p>
                        <h1>Options</h1>

                        <h4>Select widget to be shown on bottom of the each:</h4>

                        <label for="aklamatorTwitchSingleWidgetTitle">Title Above widget (Optional): </label>
                        <input type="text" style="width: 300px; margin-bottom:10px" name="aklamatorTwitchSingleWidgetTitle"
                               id="aklamatorTwitchSingleWidgetTitle"
                               value="<?php echo(get_option("aklamatorTwitchSingleWidgetTitle")); ?>" maxlength="999"/>

                        <?php

                        $widgets = $this->api_data->data;

                        /* Add new item to the end of array */
                        $item_add = new stdClass();
                        $item_add->uniq_name = 'none';
                        $item_add->title = 'Do not show';
                        $widgets[] = $item_add;

                        if (get_option('aklamatorTwitchSingleWidgetID') != '') {
                            $option = get_option('aklamatorTwitchSingleWidgetID');
                        }
                        else {
                            $option = 'Initial Twitch widget created';
                        }?>

                        <label for="aklamatorTwitchSingleWidgetID">Single post: </label>
                        <select id="aklamatorTwitchSingleWidgetID" name="aklamatorTwitchSingleWidgetID">
                            <?php
                            foreach ($widgets as $item): ?>
                                <option <?php echo ($option == $item->uniq_name || $option == $item->title) ? 'selected="selected"' : ''; ?>
                                    value="<?php echo $item->uniq_name; ?>"><?php echo $item->title; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input style="margin-left: 5px;" id="preview_single" type="button"
                               class="button primary big submit"
                               onclick="myFunction(jQuery('#aklamatorTwitchSingleWidgetID option[selected]').val())"
                               value="Preview" <?php echo get_option('aklamatorTwitchSingleWidgetID') == "none" ? "disabled" : ""; ?>>
                        </p>

                        <p>
                            <label for="aklamatorTwitchPageWidgetID">Single page: </label>
                            <select id="aklamatorTwitchPageWidgetID" name="aklamatorTwitchPageWidgetID">
                                <?php
                                foreach ($widgets as $item): ?>
                                    <option <?php echo (get_option('aklamatorTwitchPageWidgetID') == $item->uniq_name) ? 'selected="selected"' : ''; ?>
                                        value="<?php echo $item->uniq_name; ?>"><?php echo $item->title; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input style="margin-left: 5px;" type="button" id="preview_page"
                                   class="button primary big submit"
                                   onclick="myFunction(jQuery('#aklamatorTwitchPageWidgetID option[selected]').val())"
                                   value="Preview" <?php echo get_option('aklamatorTwitchPageWidgetID') == "none" ? "disabled" : ""; ?>>

                        </p>


                    <?php endif; ?>
                    <input id="aklamator_twitch_save" class="aklamator_INlogin" style ="margin: 0; border: 0; float: left;" type="submit" value="<?php echo (_e("Save Changes")); ?>" />
                    <?php if(!isset($this->api_data->flag) || !$this->api_data->flag): ?>
                        <div style="float: left; padding: 7px 0 0 10px; color: red; font-weight: bold; font-size: 16px"> <-- In order to proceed save changes</div>
                    <?php endif ?>


                </form>
        </div>

        <div style="clear:both"></div>
        <div style="margin-top: 20px; margin-left: 0px; width: 810px;" class="box">

            <?php if (isset($this->curlfailovao) && $this->curlfailovao && $this->application_id != ''): ?>
                <h2 style="color:red">Error communicating with Aklamator server, please refresh plugin page or try again
                    later. </h2>
            <?php endif; ?>
            <?php if (!isset($this->api_data->flag) || !$this->api_data->flag): ?>
                <a href="<?php echo $this->getSignupUrl(); ?>" target="_blank"><img
                        style="border-radius:5px;border:0px;"
                        src=" <?php echo AKLATWITCH_PR_PLUGIN_URL . 'images/teaser-810x262.png'; ?>"/></a>
            <?php else : ?>
            <!-- Start of dataTables -->
            <div id="aklamatorTwitchPro-options">
                <h1>Your Widgets</h1>
                <div>In order to add new widgets or change dimensions please <a
                        href="<?php echo $this->aklamator_url; ?>login" target="_blank">login to aklamator</a></div>
            </div>
            <br>
            <table cellpadding="0" cellspacing="0" border="0"
                   class="responsive dynamicTable display table table-bordered" width="100%">
                <thead>
                <tr>

                    <th>Name</th>
                    <th>Domain</th>
                    <th>Settings</th>
                    <th>Image size</th>
                    <th>Column/row</th>
                    <th>Created At</th>

                </tr>
                </thead>
                <tbody>
                <?php foreach ($this->api_data->data as $item): ?>

                    <tr class="odd">
                        <td style="vertical-align: middle;"><?php echo $item->title; ?></td>
                        <td style="vertical-align: middle;">
                            <?php foreach ($item->domain_ids as $domain): ?>
                                <a href="<?php echo $domain->url; ?>" target="_blank"><?php echo $domain->title; ?></a>
                                <br/>
                            <?php endforeach; ?>
                        </td>
                        <td style="vertical-align: middle">
                            <div style="float: left; margin-right: 10px" class="button-group">
                                <input type="button" class="button primary big submit"
                                       onclick="myFunction('<?php echo $item->uniq_name; ?>')" value="Preview Widget">
                            </div>
                        </td>
                        <td style="vertical-align: middle;"><?php echo "<a href = \"$this->aklamator_url" . "widget/edit/$item->id\" target='_blank' title='Click & Login to change'>$item->img_size px</a>"; ?></td>
                        <td style="vertical-align: middle;"><?php echo "<a href = \"$this->aklamator_url" . "widget/edit/$item->id\" target='_blank' title='Click & Login to change'>" . $item->column_number . " x " . $item->row_number . "</a>"; ?>

                            <div style="float: right;">
                                <?php echo "<a class=\"btn btn-primary\" href = \"$this->aklamator_url" . "widget/edit/$item->id\" target='_blank' title='Edit widget settings'>Edit</a>"; ?>
                            </div>

                        </td>
                        <td style="vertical-align: middle;"><?php echo $item->date_created; ?></td>


                    </tr>
                <?php endforeach; ?>

                </tbody>
                <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Domain</th>
                    <th>Settings</th>
                    <th>Immg size</th>
                    <th>Column/row</th>
                    <th>Created At</th>
                </tr>
                </tfoot>
            </table>
        <?php endif; ?>
        </div>
    </div>
    <div class="rigth" style="float: right;">
        <!-- right sidebar -->
        <div class="right_sidebar">
            <iframe width="330" height="1024"
                    src="<?php echo $this->aklamator_url; ?>wp-sidebar/right?plugin=twitch"
                    frameborder="0"></iframe>
        </div>
        <!-- End Right sidebar -->
    </div>
</div>