<!-- HERO : BEGIN -->
<tr>
    <!-- Bulletproof Background Images c/o https://backgrounds.cm -->
    <td background="{{ url('/') }}/images/email-assets/@yield('hero-background-image')" bgcolor="#222222" align="center" valign="top"
        style="text-align: center; background-position: center center !important; background-size: cover !important;">
        <!--[if gte mso 9]>
        <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false"
                style="width:680px; height:380px; background-position: center center !important;">
            <v:fill type="tile" src="background.png" color="#222222"/>
            <v:textbox inset="0,0,0,0">
        <![endif]-->
        <div>
            <!--[if mso]>
            <table role="presentation" border="0" cellspacing="0" cellpadding="0" align="center" width="500">
                <tr>
                    <td align="center" valign="middle" width="500">
            <![endif]-->
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="center" width="100%"
                   style="max-width:500px; margin: auto;">

                <tr>
                    <td height="20" style="font-size:20px; line-height:20px;">&nbsp;</td>
                </tr>

                <tr>
                    <td align="center" valign="middle">

                        <table>
                            <tr>
                                <td valign="top" style="text-align: center; padding: 60px 20px 10px 20px;">

                                    <h1 style="margin: 0; font-family: 'Montserrat', sans-serif; font-size: 30px; line-height: 36px; color: #ffffff; font-weight: bold;">
                                        @yield('hero-title')
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top"
                                    style="text-align: center; padding: 10px 20px 15px 20px; font-family: sans-serif; font-size: 15px; line-height: 20px; color: #CFCFCF;">
                                    <p style="margin: 0;">
                                        @yield('hero-subtitle')
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="center" style="text-align: center; padding: 15px 0px 60px 0px;">

                                    <!-- Button : BEGIN -->
                                    <center>
                                        <table role="presentation" align="center" cellspacing="0" cellpadding="0"
                                               border="0" class="center-on-narrow" style="text-align: center;">
                                            <tr>
                                                <td style="border-radius: 50px; background: #26a4d3; text-align: center;"
                                                    class="button-td">
                                                    <a href="@yield('hero-button-url')"
                                                       style="background: #26a4d3; border: 15px solid #26a4d3; font-family: 'Montserrat', sans-serif; font-size: 14px; line-height: 1.1; text-align: center; text-decoration: none; display: block; border-radius: 50px; font-weight: bold;"
                                                       class="button-a">
                                                        <span style="color:#ffffff;" class="button-link">&nbsp;&nbsp;&nbsp;&nbsp;@yield('hero-button-text')&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                                    </a>
                                                </td>
                                            </tr>
                                            @if(View::hasSection('hero-button-text-2'))
                                            <tr>
                                                <td style="border-radius: 50px; background: #26a4d3; text-align: center;"
                                                    class="button-td">
                                                    <a href="
                                                     @yield('hero-button-url-2')
                                                            "
                                                       style="background: #26a4d3; border: 15px solid #26a4d3; font-family: 'Montserrat', sans-serif; font-size: 14px; line-height: 1.1; text-align: center; text-decoration: none; display: block; border-radius: 50px; font-weight: bold;"
                                                       class="button-a">
                                                        <span style="color:#ffffff;" class="button-link">&nbsp;&nbsp;&nbsp;&nbsp;@yield('hero-button-text-2')&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endif
                                        </table>
                                    </center>
                                    <!-- Button : END -->

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <tr>
                    <td height="20" style="font-size:20px; line-height:20px;">&nbsp;</td>
                </tr>

            </table>
            <!--[if mso]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </div>
        <!--[if gte mso 9]>
        </v:textbox>
        </v:rect>
        <![endif]-->
    </td>
</tr>
<!-- HERO : END -->
