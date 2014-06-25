Ext.namespace(
    'Phlexible.mediatemplates.image',
    'Phlexible.mediatemplates.video',
    'Phlexible.mediatemplates.audio',
    'Phlexible.mediatemplates.pdf2swf',
    'Phlexible.mediatemplates.menuhandle'
);

Phlexible.mediatemplates.VideoFormats = [
    ['', Phlexible.mediatemplates.Strings.keep_format],
    ['flv', 'FLV'],
    ['m4v', 'M4V'],
    ['mp4', 'MP4'],
    ['wm7', 'WM7'],
    ['mov', 'MOV'],
    ['mpg1', 'MPG1']
];

Phlexible.mediatemplates.VideoFormatsWeb = [
    ['flv', 'FLV'],
    ['mp4', 'MP4']
];

Phlexible.mediatemplates.VideoBitrates = [
    ['', Phlexible.mediatemplates.Strings.keep_bitrate],
    ['300k', '300k'],
    ['500k', '500k'],
    ['800k', '800k'],
    ['1000k', '1000k'],
    ['2000k', '2000k']
];

Phlexible.mediatemplates.VideoFramerates = [
    ['', Phlexible.mediatemplates.Strings.keep_framerate],
    ['5', '5'],
    ['10', '10'],
    ['15', '15'],
    ['20', '20'],
    ['25', '25']
];

Phlexible.mediatemplates.AudioBitrates = [
    ['', Phlexible.mediatemplates.Strings.keep_bitrate],
    ['32k', '32k'],
    ['64k', '64k'],
    ['96k', '96k'],
    ['128k', '128k'],
    ['192k', '192k'],
    ['256k', '256k'],
    ['320k', '320k']
];

Phlexible.mediatemplates.AudioSamplerates = [
    ['', Phlexible.mediatemplates.Strings.keep_samplerate],
    ['11025', '11025 Hz'],
    ['22050', '22050 Hz'],
    ['44100', '44100 Hz']
];

Phlexible.mediatemplates.AudioSamplebits = [
    ['', Phlexible.mediatemplates.Strings.keep_samplebits],
    ['8', '8 bit'],
    ['16', '16 bit'],
    ['32', '32 bit']
];

Phlexible.mediatemplates.AudioChannels = [
    ['', Phlexible.mediatemplates.Strings.keep_channels],
    ['0', Phlexible.mediatemplates.Strings.channels_no],
    ['1', Phlexible.mediatemplates.Strings.channels_mono],
    ['2', Phlexible.mediatemplates.Strings.channels_stereo]
];
