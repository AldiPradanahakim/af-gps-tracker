import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Leaflet's default marker icon paths are computed relative to the CSS file
// location, which breaks once Leaflet is bundled by Vite instead of loaded
// from a CDN. Point the default icon at the bundled/hashed asset URLs
// explicitly so any L.marker(...) call that doesn't pass a custom `icon`
// option (a few call sites across the app rely on the Leaflet default pin)
// still renders correctly.
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

// The Blade partials under resources/views/**/partials/scripts reference
// Leaflet as the bare global `L` (previously supplied by the unpkg CDN
// script). Keep that contract intact so none of those inline scripts need
// to change.
window.L = L;
