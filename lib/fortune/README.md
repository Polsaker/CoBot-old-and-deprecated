Description
===========
Want to quickly display a *nix-style Fortune somewhere without having a dependency on the `fortune` program? This is a quick and dirty PHP class that lets you create and consume requests to the [Gigaset Fortune-RSS generator](http://wertarbyte.de/gigaset-rss/), which provides over 150 different "cookie jars" of fortunes to pick from in 4 different languages.

License
-------
This class is licensed under the Apache License, Version 2.0. Obviously I hold no rights to the RSS generator or the fortunes returned by it, nor do I have any way of knowing what those licenses are. If you're worried about that kind of thing, this probably isn't a good idea.

	Copyright 2012 Chris Meller

	Licensed under the Apache License, Version 2.0 (the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

	    http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.

Usage
=====

Include the class, and get kicking.

To get a single textual fortune in English, including possibly offensive ones:

````echo Fortune::factory()->languages( 'en' )->display_offensive( true )->get_fortunes( true )->text;````

You can also get an array of multiple fortunes. For example, to get 5 in a mix of English and German, including fortunes over 140 characters ("long"), but only from the Star Trek jar:

	$fortunes = Fortune::factory()
		->fortunes( 5 )
		->languages( array( 'en', 'de' ) )
		->display_long( true )
		->jars( array( 'startrek' ) )
		->get_fortunes();

	foreach ( $fortunes as $fortune ) {
		echo $fortune->text . "\n";
	}

Each `$fortune` object will also have a `$jar` property indicating the cookie jar it came from.
