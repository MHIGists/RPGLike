# RPGLike plugin for Minecraft Bedrock [WIP]
This plugin aims to add more higher RPG feel to minecraft
### Features:
 * 4 basic attributes each upgradable with a skill point earned by leveling up.
 * 4 basic skills currently unlocked at lvl 10 of the respective attribute.
 * UI for all of those
 * Almost everything configurable
### For devs:
 * You can easily create new skills by extending the BaseSkill and adding some Listeners. Go trough the 4 example skills to grasp the Skill Structure you need to extend.
 * Easily access player objects using the PlayerManager, example: 
```php
PlayerManager::getPlayer($playerName);
```
### TODO:
 * Implement cooldown for skills
 * Come up with ideas for active skills
 * Finish RPGPlayer->checkForSkills()
 * Change function visibility to enforce plugin stability
 * Add more customization trough config
