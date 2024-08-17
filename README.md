
# PushNotificationMobile

This project enables sending push notifications to a mobile app using PHP on your server. It supports push notifications for both Android and iOS devices through different token types.

## Features
- Push notifications to mobile apps using server-side PHP.
- Supports Android (FCM Token) and iOS (APNS Token).
- Retrieve device tokens using libraries like Firebase or Expo-Notification.

## Usage

To send a notification, use the following function:

\`\`\`php
$push= new pushNotification();
$push->createNoti($token, $title, $content);
\`\`\`

## Required Information

### iOS: PHP Push Notifications with APNS

To set up push notifications for iOS devices, you'll need:

- \`$authKey\`: The .p8 file, e.g., "AuthKey_<KEY_ID>.p8".
- \`$teamId\`: Your Apple Developer Team ID, e.g., 'HAHASDHAD'.
- \`$tokenId\`: The ID of your .p8 key, e.g., 'ADASDASD'.
- \`$bundleId\`: The Bundle ID of your app, e.g., 'com.abc.abc'.

### Android: PHP Push Notifications with FCM

To set up push notifications for Android devices, you'll need:

- \`$key\`: The FCM Key from Firebase, e.g., "AAAAX3e0lxQ:AFHAJFSHADJFSDFJSDFDFigawpeIEWcm0mMEBwBeR...".

### PHP Push Notifications with HTTPv1 (Android)

1. Generate a \`private key\` in your Firebase Service Account.
2. Save the \`private_key.json\` file on your server.
3. Update your code with the following:
   \`\`\`php
   $adr = 'Address folder';
   $privatekey = 'your_private_key.json';
   \`\`\`

Good luck!
