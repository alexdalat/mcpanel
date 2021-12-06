# mcpanel

## Dependencies
 - An Apache server with PHP enabled and increased file upload limits
 - A mysql database with properly configured 'servers' and 'users' tables
 - A cloudflare account for the dns management
 - A portforwarded network

This was one of my biggest, most long-term projects dating all the way back to my first programming days.<br />
It was originally created as a single console so that my friend could help me manage our server whenever I was busy.<br />
I've never liked paying for things that I can do myself so the project grew in order to replicate stereotypical hosting panels.<br />
It then developed (over the span of roughly 7 years) into a fully functioning panel which allowed for full server control, including creating and deleting new servers.


Sadly a lot of development (1 to 1.5 years) got lost in a server failure. It included a DNS management page, file explorers for every server, a transfer to PostGres database protocols, API implementations, and a few other things. I learned my mistake and I now always upload to github before pushing to production...

This project expanded my knowledge and love for programming and now that I have no use for it anymore I am releasing it to the public in hopes that it helps someone.

![Example 1](https://github.com/Xeladarocks/mcpanel/blob/master/preview_imgs/example_v1.0_1.png?raw=true)
