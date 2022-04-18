vcl 4.0;

import std;

sub vcl_hash {
    if (req.http.X-UA-Device ~ "^mobile"
        || req.http.X-UA-device ~ "^tablet"
    ) {
        hash_data("mobile");
    } else {
        hash_data("desktop");
    }
}
