/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

const es = new EventSource('https://teamtask-manager.local:3001/.well-known/mercure?topic=test');
es.onmessage = e => console.log(e.data);

import '../bootstrap.js';
import './utils/kyManager.ts';
import './components/flashMessage.ts';
import '../../vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js';
import '../styles/app.css';