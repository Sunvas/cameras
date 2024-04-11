## Cameras &mdash; *a simple video surveillance single-page application for reading webrtc streams from mediamtx*

#### It is implied that:
1. You have properly functioning hosting with reachable domain `https://example.com` .
2. You have already configured [mediamtx](https://github.com/bluenviron/mediamtx) and it is possible to read streams by visiting `https://www.example.com:8889/stream` .


#### Deployment:
1. Upload contents of `build` folder to `https://example.com/cameras/` .
2. Restrict access to `https://example.com/cameras/restricted/` via http authentication .
3. Configure file `https://example.com/cameras/restricted/cameras.json` :
	- `caption` - will be displayed on application's header.
	- `host` - specifies domain and port where mediamtx server is run.
	- `streams` - array of arrays of streams where
		- `caption` specifies stream caption for humans
		- `stream-name` specifies stream name on mediamtx server
		- `material-icon` specifies stream icon from [Material Icons](https://fonts.google.com/icons) collection.
		- in addition you can rotate stream display clockwise or counterclockwise providing fourth element like `{"rotate":90}` or `{"rotate":-90}`.
4. Edit files `https://example.com/cameras/restricted/index.php` and `https://example.com/cameras/auth.php`: replace `your_secret_key_should_be_here` by your own private secret key (should be equal for both files).
5. In your `mediamtx.yml` find parameter `authHTTPAddress` and set there the link `https://example.com/cameras/auth.php`.