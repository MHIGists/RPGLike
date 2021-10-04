<div>
<br />
<p align="center">
<h3 align="center">RPGLike</h3>

  <p align="center">
    This plugin aims to add higher RPG feel to Minecraft adding attributes and skills.
    <br />
    <br />
    <br />
    <a href="https://github.com/MHIGists/RPGLike/issues">Report Bug</a>
    ·
    <a href="https://github.com/MHIGists/RPGLike/issues">Request Feature</a>
  </p>
</p>
</div>



<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary><h2 style="display: inline-block">Table of Contents</h2></summary>
  <ol>
    <li>
      <a href="#about-the-project">About The Project</a>
    </li>
    <li>
      <a href="#getting-started">Getting Started</a>
      <ul>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#roadmap">Roadmap</a></li>
  </ol>
</details>



<!-- ABOUT THE PROJECT -->
<div id="about-the-project">

## About The Project
Started this as a side project. I'm currently working full time so update frequency may vary<br>
depending on my workload. My aspirations for this project is ultimately creating a mini-game<br>
with as much as storyline and quests so that's something to look forward.Until then <br>
we have something that slightly enhances player in-game experience but it's still fun.<br>
I'm grateful for every contribution you make. Even as much as testing takes a lot of my time which I don't have.

</div>


<!-- GETTING STARTED -->
<div id="getting-started">

## Getting Started

</div>

### Installation 
<div id="installation">
Just open the releases tab and download one of the official releases, then place it in the plugins folder.
</div>

<!-- USAGE EXAMPLES -->
## Usage

<div id="usage">
Aftrer installation and initial start you can check the plugin data folder of the plugin<br>
to configure the plugin to your liking.<br>

### messages.yml:<br>
Here you can change every string that's displayed to the player.
###  players.yml
Here are all the players that've connected to the server with all their attributes.
### config.yml
Here are all the settings of the plugin. Here is a quick rundown of the settings you'll need.<br>
#### keep-xp:<br>
Pretty self-explanatory this setting has 2 values. true or false. Controls whether the player<br>
loses all attributes,skills,traits and levels when they've died.
#### discovery:
This setting has 2 values true or false. When set to true it will hide every skill and trait until the<br>
player have unlocked it.<br>
When set to false all skills and traits will be visible in the /rpg menu.
#### Hud
Here you can turn on or off the included HUD. Change the message it transmits, its period in which its transmitted<br>
and the timezone of the server.
#### Skills
Here are all the skills the plugin has. Here is the Tank skills for example.<br>
If you look at [levels]() you will see under it all the available levels for this particular skill.<br>
You are free to add new levels to your liking. Just be sure to keep the spacing of it. As a hint you can copy level 2<br>
place it right under it. From there you can simply change 2 => 3 and change its values. Now here are the values<br>
under [unlock]() you have first the Attribute (VIT,DEX,STR or DEF) you can freely change this as you like. <br>
on the right of the Attribute you have the points a player must have in order to unlock it. If you want to add more<br>
Attributes to unlock a skill just add a new line under unlock with the next requirement in the format [Attribute]() : [Points]()<br>
Under [unlock]() you have [chance]() from there you can configure the chance at which a skill's effect will trigger itself.<br>
<br>
That about explains the [levels]() the next one is [range]() when an active or passive skill has AOE range this is where<br>
it's configured. The value is in blocks from the source player.<br>
<br>
[max_entities_in_range]() this option allows to set the maximum entities in the [range]() of the skill that will be affected<br>
by the effect of the skill. The value is an integer.<br>
<br>
[is_aoe]() controls whether the skills is aoe or not. If this option is set to true please specify [range]() and [max_entities_in_range]()
<br>
[cooldown]() controls the cooldown of an active ability it's value is an integer in seconds<br>
<br>
[is_active]() has 2 values true and false, when set to true the skill is considered active and should have a [cooldown]().<br>
<br>

#### Modifiers
Those are used to modify how much a player gains per point in an attribute. For example DEX with a value of 0.005 will be multiplied by the points in DEX.
Try experimenting with these to achieve the desired strength of attributes.


</div>

#### Traits
Here you can create whatever player traits you want! The constraints however are<br>
that you the counter of each trait is connected to the 3 types of [action]() a trait currently<br>
support. Those are "break", "kill", "pickup". "break" counts each block that's been broken<br>
by the player. Those blocks can be set from the [blocks]() option which takes values separated by a comma.
Same with the others "pickup" counts blocks or any items picked up. And kill counts entity killed<br>
that includes players.
[levels]() is the same as in the skills [section]().

<!-- ROADMAP -->
## Roadmap

<div id="roadmap">
  
### Current objectives:
* Finishing the trait system with the 3 included actions.<br>
* Active bug hunting
* More configuration for settings (there is still some hard coded stuff)
* Adding permissions and more control from in-game commands or maybe an admin form?
* Party system<br>
After this have been completed an official release will be made until then every release should be considered a heavy WIP. 
</div>
You can contact me any time on Discord StrawberryMilk#3229

See the [open issues](https://github.com/MHIGists/RPGLike/issues) for a list of proposed features (and known issues).
