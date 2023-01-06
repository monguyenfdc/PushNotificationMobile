# PushNotificationMobile
 Creact notification to mobile app with PHP
 Function push notification server to mobile app with diver token
 Android: FCM Token
 ISO: APNS Token
 You can get Token with lib firebase, expo-notification...

# To used:
createNoti($token, $title, $content)

# information need to creact:
# IOS: PHP push notification with APNS
	$authKey = "AuthKey_<ADASDASD>.p8"; 	// => Name .p8 file
	$teamId = 'HAHASDHAD';					// => team ID from account developer
	$tokenId = 'ADASDASD';					// => key .p8 ID
	$bundleId = 'com.abc.abc';				// => Bundle ID from App
 
# Android: PHP push notification with FCM
 $key="AAAAX3e0lxQ:AFHAJFSHADJFSDFJSDFDFigawpeIEWcm0mMEBwBeR..."; // => FCM KEY from firebase
