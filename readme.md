# Content Changes for 2.1.0 Rollout

 * [x] remove photo credits from slider posts (they're handled by the alt text now)
   - Katina's done this on production 
 * [ ] upgrade WP to 4.0
 * [ ] disable attachments in BP Docs. (Dashboard -> Buddypress Docs -> Settings -> Disable Attachments) 
 * [ ] fetch and check out the buddypress-docs branch `mla`. This branch has the feature that will allow us to disable attachments but show existing attachments. 
 * [ ] check out CACAP from our repo: `git clone https://github.com/mlaa/cac-advanced-profiles.git`
 * [ ] get CACAP dependencies via git submodules: `git submodule update --init --recursive` 
 * [ ] Network Dashboard -> Users -> CAC Advanced Profiles -> Tab: Profile Header (Public): drag the following fields to their places: 
   - Institutional or Other Affiliation -> Brief Descriptor
   - Title -> About You
 * [ ] Network Dashboard -> Users -> CAC Advanced Profiles -> Tab: Profile Header (Edit Mode): make sure fields are in this order: 
   - Left column: Name, Institutional or Other Affiliation
   - Right column: Title
 * [ ] Network Dashboard -> Users -> Profile Fields -> Title -> Edit: 
   - description: e.g. "Adjunct Instructor" 
 * [ ] Network Dashboard -> Users -> Profile Fields -> Institutional or Other Affiliation -> Edit: 
   - description: e.g. "College of Yoknapatawpha" 


##Optional Changes
 * [ ] apply [Buddypress patch #5858](https://buddypress.trac.wordpress.org/ticket/5858), which fixes #104 with site searches incorrectly reporting the number of results. 
 * [ ] apply [Buddypress patch #5872](https://buddypress.trac.wordpress.org/ticket/5872), which adds auto-linking ability for semicolon-separated lists in xprofile

# Commons-in-a-Box MLA Child Theme

This theme was developed for [_MLA Commons_][1]. It functions as a child 
theme of the [official Commons-in-a-Box theme][2].

The source code of this theme is released under the GPLv2 (see LICENSE.txt). 
The images are included for reference but remain the property of the Modern 
Language Association.

[1]: http://commons.mla.org
[2]: https://github.com/cuny-academic-commons/cbox-theme

# Changes in Version 2.1.0 

 * Buddypress-docs has a cleaned-up interface. It no longer gives users the arguably unnecessary options of changing the associated group of a doc, or of changing its access rights or parent doc.  

# Changes in Version 2.0 

 * The site has been given a new, flat theme. Gradients, textures, and 3D buttons have been replaced by simple, minimalistic elements. The colorscheme has been also simplified, favoring desaturated colors and grayscale over the bright reds and oranges of the previous theme. 
 * Many redundant UI elements have been removed. Some subscription links have been removed in favor of the "Email Subscriptions" group tab. Pagination buttons that previously appeared on the top and bottom of any content area now only appear at the bottom. Forums titles that bear the same titles as their groups have been removed. 
 * The default homepage for groups is now "Forum." The group tab previously called "Home" is now more accurately termed "Activity." 
 * The sitewide wiki has been removed. However, documents created there are still available in user profiles, and may still be associated with groups. 
 * Help-related items on the homepage have been moved to a dropdown item revealed by clicking the homepage menu item "help." 
 * Group announcements have been removed. Group administrators who wish to make announcements may do so by making forum posts.  
 * The "Docs" plugin has been redesigned in a new, minimal style that transforms formerly tabular data into easier-to-read prose.  
