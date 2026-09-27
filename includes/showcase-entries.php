<?php
// Community showcase entries. Anyone running OpenRTMP in a real project,
// deployment, or integration can add themselves here. The same list is shown
// on the English (/showcase/) and German (/de/showcase/) pages. English
// descriptions are fine; add an optional 'description_de' to show a German
// one on /de/showcase/.
//
// How to add your project:
//   1. Open a submission: https://github.com/OpenRTMP/community/issues/new?template=showcase_submission.yml
//   2. Or open a pull request against this file directly, adding one entry
//      below (alphabetically by name) in the same shape as the example:
//
//   [
//     'name' => 'Your project name',
//     'url' => 'https://example.com',
//     'description' => 'One or two sentences on what it is and how it uses OpenRTMP.',
//     'tag' => 'Deployment', // Deployment, Integration, Library, or Community project
//   ],
//
// Keep descriptions factual and first-person-verifiable (what you actually
// built or run), not marketing copy. Logos/screenshots are not required.
$showcaseEntries = [
  [
    'name' => 'IRL.com.de',
    'url' => 'https://irl.com.de',
    'description' => 'IRL streaming platform that runs its RTMP and RTMPS relay servers on librtmp2.',
    'description_de' => 'IRL-Streaming-Plattform, deren RTMP- und RTMPS-Relay-Server auf librtmp2 laufen.',
    'tag' => 'Deployment',
  ],
];
