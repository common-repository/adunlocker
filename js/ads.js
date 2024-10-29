/**
 * AdUnlocker
 * Powerful browser adblock blocker.
 *
 * @encoding        UTF-8
 * @copyright       (C) 2021 PixPal ( https://pixpal.net/ ). All rights reserved.
 * @support         help@pixpal.net
 **/

"use strict";

/** Creates a hidden div. */
let e = document.createElement( 'div' );
e.id = 'pp-AdUnlocker-ads';
e.style.display = 'none';
document.body.appendChild( e );
