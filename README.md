# synologyTwilio
A Simple Script to use Twilio as your SMS outgoing provider for notifications

## Problem:
Synology NAS devices allow you to receive notifications from the NAS via email and SMS. However, SMS seems to only work on a few included providers.

While the option exists to "Add SMS provider" the reality is that the POST method does not seem to work and Twilio's API for sending SMS does not respond to the GET method.
Persons have been asking for a fix since at least 2021 to this. But Synology likely has better and more important things to do.

This script is a work around which sits on the local NAS, using the Webstation. The script is called with the GET method, then everything is sent to Twilio via the POST method.
Realistically this relaying could have been achieved by simply calling the script and the script calling out to Twilio's API. 
However, setting up the "Add SMS provider" portion of the notifications in the Synology desktop turned out to be a bit of a pain. Which I help address in this script.

## Requirements:
A synology NAS. (This should work on other NAS, but your setup will vary.)
Synology Webstation installed.
  PHP installed (Tested on versions 7 and 8)
    cUrl Extension active in the PHP install. (You'll get an error if the extension is not detected.)

## Step One:
If you haven't already installed the Webstation program, do so in the synology Package center and make sure it's running. Make sure that under "PHP Settings" [DSM 6] or "Script Language Settings" [DSM 7] you have PHP installed and working. Under the PHP profile make sure you have "cURL" selected.

## Step Two:
Upload the script (smsRelay.php) to your Synology NAS webStation directory and surf to it from a browser of your choosing. (eg. http://1.2.3.4/smsRelay.php)
  If you are unable to surf to the script, Webstation is probably not configured correctly.
  If you can get to the script but it just download's the script, then PHP is not enabled.
  If you can get to the script but there is a large warning, you probably should have read the part about cURL needing to be enabled. Go enable cURL and refresh the page.

## Step Three:
With the page loaded and cURL working, you should see a form with 5 inputs.
  The sid is taken from your Twilio Console.
  The token is taken from your Twilio Console as well.
  The body should be set to "Hello World" always set it to this. Otherwise your synology will, for some reason get cranky.
  The To field is to be populated with the number you are going to send to. I have not tried non-North American numbers. The format here should be 15551112222 No dots, dashes or spaces. No starting with a plus sign.
  The From field is to be populated with the number you are going to send from. The "from" number must be a number that you own with Twilio and can control with the associated sid and token. Again, I have not tried non-North American numbers. The format here should be 15551112222 No dots, dashes or spaces. No starting with a plus sign.

  Now you can click "test".

## Step Four:
You will now have results. 
  If you have any errors, the script will advised you in a red banner below the form. Fix your problems and test again.
  If all was well, and no error was returned, you should get a banner telling you that the test was a success. Below that you will get a string that was made specifically for your synology NAS that the script is currently running on.
    Copy the string to the clipboard.

## Step Five:
In the Synology NAS control panel, navigate to Notification
  Make sure "Enable SMS notifications" in checked.
  Click "Add SMS Provider"
  For "Provider Name" you can put "smsRelay" or whatever you want.
  For "SMS URL" paste in the string that was generated by the script.
  Leave the radio box for "HTTP method" on "GET"
  Click "Next"

  The next screen is "Edit HTTP request header" there is nothing to do here. Click "Next"

  The final screen has a series of dropdowns which corespond to the GET values in the string. They should be set as the following:
  ###  sid   > Other
  ###  token > Other
  ###  body  > Message Content
  ###  to    > Phone Number
  ###  from  > Sender

  Once these are set, you should be able to press "Apply" and have the window close.
  If you have the "body" in the string set to anything other than "body=Hello World" you may run into an error. I already warned you about this!

## Step Six:
Back in the the notifications window of the control panel, click "apply" at the bottom. Sometimes, failing to do this causes problems with it saving.
Once you've clicked "Apply" click "Send a Test SMS Message"

## Optional Step Seven:
Enable SMS interval should be used. If the NAS goes nuts, it's going to start cranking out 1000s of SMS messages. Set this to once a minute and you'll be fine... most likely.
