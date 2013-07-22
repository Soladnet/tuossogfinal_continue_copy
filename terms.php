<?php
if (isset($_COOKIE['user_auth'])) {
    include_once './encryptionClass.php';
    include_once './GossoutUser.php';
    $encrypt = new Encryption();
    $user = new GossoutUser(0);
    $id = $encrypt->safe_b64decode($_COOKIE['user_auth']);
    if (is_numeric($id)) {
        $user->setUserId($id);
        $user->getProfile();
    } else {
        include_once './LoginClass.php';
        $login = new Login();
        $login->logout();
    }
}
?>
<!doctype html>
<html lang="en">
    <head>
        <title>Gossout - Term of Service</title>
        <script src="scripts/jquery-1.9.1.min.js"></script>
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.css">
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script>
            $(function() {
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" /></a>');
                }
                $("#searchField").focus();
            });
        </script>
        <style>
            ul li{
                margin-top: .5em;
            }
        </style>
    </head>
    <body>
        <div class="index-page-wrapper">	
            <div class="index-nav">
                <span class="index-login" id="name-login-cont"><?php
                    echo isset($user) ? "Welcome <a href='home'>" . $user->getFullname() . "</a> [ <a href='login_exec'>Logout</a> ]" :
                            'Already have an account? <a href="login">Login Here</a> | <a href="signup-personal">Sign up</a>'
                    ?></span>
                <div class="clear"></div>
            </div>
            <div class="index-banner">
                <div class="logo" id="logo"><img alt=""></span></div>
            </div>
            <center><h3>Terms of Use</h3></center>
            <p>&nbsp;</p>           
            <p>&nbsp;</p>           
            <p>&nbsp;</p>
            <div class="index-intro">

                <?php ?>

                <?php ?>
                <p>&nbsp;</p>
                <center>
                    <div style='border-radius:5px;border:1px #ccc solid;background: white;width: 90%;height: auto;text-align: left;'>
                        <div style="margin: 2em;margin-top:1em;text-align:justify">
                            Gossout gives you a personal, worldwide, royalty-free, non-assignable and non-exclusive license to use the software that is provided to you by Gossout as part of the Services. This license is for the sole purpose of enabling you to use and enjoy the benefit of the Services as provided by Gossout, in the manner permitted by these terms.
                            <ul style="list-style-type:disc">
                                <li style='text-align:justify'><strong>RESTRICTIONS ON CONTENT AND USE OF SERVICES</strong><br/>
                                    We reserve the right at all times (but will not have an obligation) to remove or refuse to distribute any Content on the Services, to suspend or terminate users, and to reclaim usernames without liability to you. We also reserve the right to access, read, preserve, and disclose any information as we reasonably believe is necessary to
                                    <ul style="margin-left:2em;list-style-type:lower-roman;">
                                        <li>Satisfy any applicable law, regulation, legal process or governmental request.</li>
                                        <li>Enforce the Terms, including the investigation of potential violations hereof.</li>
                                        <li>You will not engage in unlawful multi-level marketing, such as pyramid scheme on Gossout. </li>
                                        <li>Detect, prevent, or otherwise address fraud, security or technical issues </li>
                                        <li>Respond to user support requests.</li>
                                        <li>Protect the rights, property or safety of Gossout, its users and the public. </li>
                                        <li>You will not post content that: is hate speech, threatening or pornographic; incites violence, or contains nudity or graphic or gratuitous violence.</li>
                                    </ul>

                                </li>
                                <li><strong>SPECIAL PROVISIONS APPLICABLE TO ADVERTISERS</strong><br/>
                                    You can target your desired audience by buying ads on Gossout. The following additional terms apply to you if you place an order through our online advertising portal (order):
                                    <ul style="margin-left:2em;list-style-type:lower-roman;">
                                        <li>When you place an order. You will tell us the type of advertising you want to buy, the amount you want to spend, and your bid. If we accept your order, we will deliver your ads as inventory becomes available. When serving your ads, we do our best to deliver the ads to the audience you specify, although we cannot guarantee in every instance that your ads will reach its intended target.</li>
                                        <li>In instances where we believe doing so will enhance the effectiveness of your advertising campaign, we may broaden the targeting criteria you specify.</li>
                                        <li>You will pay for your orders in accordance with our Payments Terms.</li>
                                        <li>Your ads will comply with our Advertising Guidelines.</li>
                                        <li>We will determine the size, placement, and positioning of your ads.</li>
                                        <li>We do not guarantee the activity that your ads will receive, such as the number of clicks your ads will get.</li>
                                        <li>We cannot control how clicks are generated on your ads. But we are 	not responsible for click fraud, technological issues, or other potentially invalid click activity that may affect the cost of running ads.</li>
                                        <li>We can use your ads and related content and information for marketing or promotional purpose.</li>
                                        <li>You will not issue any press release or make public statements about your relationship with Gossout without our prior written permission.</li>
                                        <li>We may reject or remove any ads for any reason.</li>
                                        <li>If you are placing ads on someone else's behalf, you must have permission to place those ads, including the following:</li>
                                        <ul style="margin-left:2em;list-style-type:square;">
                                            <li>You warrant that you have the legal authority to bind the advertiser to this statement</li>
                                            <li>You agree that if the advertiser you represent violates this statement, we may hold you responsible for that violation</li>
                                        </ul>
                                    </ul>
                                </li>
                                <li><strong>TERMINATION OF TERMS</strong><br/>
                                    Gossout users provide their real names and information, and we need your help to keep it that way. Here are some commitments you make to us relating to registering and maintaining the security of your account.
                                    The Terms will continue to apply until terminated by either you or Gossout as follows:
                                    You may end your legal agreement with Gossout at any time for any reason by deactivating your accounts and discontinuing your use of the Services. You do not need to specifically inform Gossout when you stop using the Services. If you stop using the services without deactivating your accounts, your accounts may be deactivated due to prolonged inactivity.   
                                    We may suspend or terminate your accounts or cease providing you with all or part of the Services at any time for any reason, including, but not limited to, if we reasonably believe: 
                                    <ul style="margin-left:2em;list-style-type:lower-roman;">
                                        <li>
                                            You have violated these Terms, </li>
                                        <li> You create risk or possible legal exposure for us; or </li>
                                        <li> Our provision of the Services to you is no longer commercially viable. 
                                        </li>

                                    </ul>
                                    We will make reasonable efforts to notify you by email address associated with your account or the next time you attempt to access your account.
                                </li>
                                <li><strong>DISCLAIMERS AND LIMITATIONS OF LIABILITY</strong><br/>
                                    Please read this section carefully since it limits the liability of Gossout and its parents, subsidiaries, affiliates, related companies, officers, directors, employees, agents, representatives, partners, and licensors (collectively called the "Gossout Entities"). Each of the subsections below applies up to the maximum extent permitted under applicable law.

                                    If any one brings a claim against us related to your actions, Content or information on Gossout, you will indemnify and hold us harmless from and against all damages, losses and expenses of any kind (including reasonable legal fees and costs) related to such claim. Although we provide rules for user conduct, we do not control or direct users actions on Gossout and are not responsible for the Content or information users transmit or share on Gossout. We are not responsible for any offensive, inappropriate, obscene, unlawful or otherwise objectionable Content or information you may encounter on Gossout. We are not responsible for the conduct, whether online or offline, or any user of Gossout.
                                    WE TRY TO KEEP GOSSOUT UP, BUG-FREE, AND SAFE, BUT YOU MAY USE IT AT YOUR OWN RISK.WE ARE PROVIDING GOSSOUT AS IS WITHOUT ANY EXPRESS OR IMPLIED WARRANTIES INCLUDING, BUT NOT LIMITED TO, IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE,  AND NON-INFRINGEMENT. WE DO NOT GUARANTEE THAT GOSSOUT WILL ALWAYS BE SAFE, SECURE OR ERROR FREE OR THAT GOSSOUT WILL ALWAYS FUNCTION WITHOUT DISRUPTIONS, DELAYS OR IMPERFECTIONS. GOSSOUT IS NOT RESPONSIBLE FOR THE ACTIONS, CONTENT, INFORMATION, OR DATA OF THIRD-PARTIES, AND YOU RELEASE US, OUR DIRECTORS, OFFICERS EMPLOYEES, AND AGENTS, FROM ANY CLAIMS AND DAMAGES, KNOWN AND UNKNOWN, ARISING OUT OF OR IN ANY WAY CONNECTED WITH ANY CLAIM YOU HAVE AGAINST ANY SUCH THIRD PARTIES. WE WILL NOT BE LIABLE TO YOU FOR ANY LOSS PROFITS OR OTHER CONSEQUENTIAL, SPECIAL, INDIRECT, OR INCIDENTAL DAMAGES ARISING OUT OF OR IN CONNECTION WITH THIS STATEMENT ON GOSSOUT, EVEN IF WE HAVE BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. GOSSOUT'S LIABILITY WILL BE LIMITED TO THE FULLEST EXTENT PERMITTED BY APPLICABLE LAW.
                                    The Gossout Entities make no warranty and disclaim all responsibility for:
                                    <ul style="margin-left:2em;list-style-type:lower-roman;">
                                        <li>
                                            The completeness, accuracy, availability, timeliness, security or reliability of the Services or any Content; 
                                        </li>
                                        <li>
                                            Any harm to your computer system, loss of data, or other harm that result from your access to or use of the Services or any Content; 
                                        </li>
                                        The deletion of, or the failure to store or to transmit, any Content and other communications maintained by the Services; and 
                                        <li>
                                            Whether the Services will meet your requirements or be available on an uninterrupted, secure, or error-free basis. No advice or information, whether oral or written, obtained from the Gossout Entities or through the Services, will create any warranty not expressly made herein.  
                                        </li>
                                    </ul>

                                </li>

                                <li><strong>LINKS</strong><br/>
                                    The Services may contain links to third-party websites or resources. You acknowledge and agree that the Gossout Entities are not responsible or liable for; (i) the availability or accuracy of such websites or resources; or (ii) the content, products, or services on or available from such websites or resources. Links to such websites or resources do not imply any endorsement by the Gossout Entities of such websites or resources or the content, products, or services available from such websites or resources. You acknowledge sole responsibility for and assume all risk arising from your use of any such websites or resources
                                </li>
                                <li><strong>WAIVER AND SEVERABILITY</strong><br/>
                                    The failure of Gossout to enforce any right or provision of these Terms will not be deemed a waiver of such right or provision. In the event that any provision of these Terms is held to be invalid or unenforceable, then that provision will be limited or eliminated to the minimum extent necessary, and the remaining provisions of these Terms will remain in full force and effect
                                </li>
                                <li><strong>CONTROLLING LAW AND JURISDICTION</strong><br/>
                                    These Terms and any action related thereto will be governed by the laws of the Federal Republic of Nigeria. All claims, legal proceeding or litigation arising in connection with the services will be bought solely in Federal or State High Courts in Nigeria.
                                </li>
                                <li><strong>ENTIRE AGREEMENT</strong><br/>
                                    These Terms and conditions are the entire and exclusive agreement between Gossout and you regarding the Services, and these Terms supersede and replace any prior agreements between Gossout and you regarding the Services. Other than members of the group of companies of which Gossout is the parent, no other person or company will be third party beneficiaries to the Terms.   
                                    We may revise these Terms from time to time; the most current version will always be at www.gossout.com. If the revision, in our sole discretion, is material we will notify you via a Gossout update or email to the email associated with your account.  By continuing to access or use the Services after those revisions become effective, you agree to be bound by the revised Terms.
                                    These Services are operated and provided by Gossout.com.  If you have any questions about these Terms, please contact us.
                                    <p>&nbsp;</p>   
                                    <p>Effective: 8th May 2013.</p>   
                                </li>
                            </ul>
                        </div> 
                    </div>
                </center>
                <p>&nbsp;</p>

                <?php ?>
            </div>
            <div class="index-shadow-bottom"></div>
            <div class="index-content-wrapper">
                <span id="footer-links">
                    <?php
                    include("footer.php");
                    ?>
                </span>
            </div>


        </div>

    </body>
</html>