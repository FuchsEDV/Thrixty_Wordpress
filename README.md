# Thrixty_Wordpress
360° Photography Player for Wordpress

ToC:
* 1.: Thrixty Player for Wordpress
* 2.: Base Repository
* 3.: Installation
* 4.: Usage
* 5.: Change Log
* 6.: Planned Features and Changes
* 7.: License

### 1.) Thrixty Player for Wordpress
This Wordpress plugin is using shortcodes to generate content fit for use with the Thrixty Player.
You can define some default options that will help you displaying your objects in the same fashion.

### 2.) Base Repository
This project is built upon the [Thrixty Player](https://github.com/FuchsEDV/Thrixty).

If you need some example files, download the "example"-folder from [here](https://github.com/FuchsEDV/Thrixty_Example).

### 3.) Installation
* Download, unpack and activate this Plugin.
* In the Wordpress-backend go to Settings -> Thrixty Player
* Read the description underneath the Options, to know what the options are and what they are doing.
* When generating Shortcodes, you will be asked for the not set Options. Do yourself a favor and fill all of them. (When serving many pictures, it is good to set up a designated server.)
* Save your Options.
* Create a new unpublished page, so you can privately examine, how this Plugin works.
* In the TinyMCE Editor should now be a new Button. It is blue and shows the 360Shots Logo.
* Press this and fill in the blanks. Options that you already set, are overwriteable, in case you need specific settings.
* After pressing "Generate" you should see a Shortcode like this: `[thrixty filelist_path_small="small_example.txt" filelist_path_large="large_example.txt" ]`
* Save the Changes and preview the Page.
* At this point you should propably have something like "Firebug" (for Firefox) installed. There are different programs for each Browser. In case something went wrong, you need to be able to view your console, as Thrixty logs its errors in there.
* When everything went good, you should now look at your rotating object. Read further on how to use.

### 4.) Usage
######Mouse/Touch:
```txt
[Click/Tap](single) => Play/Pause
[Click/Tap](double) => Zoom on/off
[Drag/Swipe] => Stop automatic rotation and turn the object. Works also in Zoom mode.
```
######Keboard:
(To use these, the Player needs to be focused - click once inside the Player.)
```txt
[Spacebar] => Play/Pause
[Arrow Left] => Step Backward
[Arrow Right] => Step Forward
[Arrow Up] => Increase Speed
[Arrow Down] => Decrease Speed
[F] => Fullscreen on/off
[ESC] => Stop Zoom, Rotation, Fullscreen all at once.
```

### 5.) Change Log
* V1.0 (Release):
    * Automatic embedding of base files
    * Shortcode Generator
    * Central Settings

### 6.) Planned Features and Changes
* Change the setting fields to textfield and radiobutton respectively.

### 7.) License
```txt
Thrixty Player for Wordpress Copyright (C) 2015  F.Heitmann @ Fuchs EDV GmbH for 360Shots

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation version 3.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
```
