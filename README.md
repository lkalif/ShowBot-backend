       _____ __                  ____        __ 
      / ___// /_  ____ _      __/ __ )____  / /_
      \__ \/ __ \/ __ \ | /| / / __  / __ \/ __/
     ___/ / / / / /_/ / |/ |/ / /_/ / /_/ / /_  
    /____/_/ /_/\____/|__/|__/_____/\____/\__/  

ShowBot by lkalif

### About

This is the web backend part of the ShowBot system used for collecting 
suggestions from a IRC channel. Those suggestions are then sent to
a web backend where people can vote on them.

The IRC bot part of this system is available on
[GitHub](https://github.com/lkalif/ShowBot)

### Installation

You will need a web server with PHP (5.5 or newer), and MySQL.
In lib folder copy config file template

    cp lib/init.php.dist lib/init.php

Then edit this file to set proper database details, passwords,
allowed channels, etc.

### License

Copyright (c) 2014 Latif Khalifa

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

