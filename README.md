| <img src="/preview/preview1.png"/> |
| ---------------------------------- |

-   [Source](https://yajrabox.com/docs/laravel-datatables/10.0/installation)

```
composer require yajra/laravel-datatables:^9.0
```

```
//app.php
'providers' => [
    // ...
    Yajra\DataTables\DataTablesServiceProvider::class,
],
```

```
php artisan vendor:publish --tag=datatables
```

-   [Bootraps](https://getbootstrap.com/docs/5.3/getting-started/introduction/)
-   [datatables](https://datatables.net/)

-   partial-action.blade.php

```html
<a href="#">{{ $model->name }}</a>

{{-- nama defaultnya adalah $model --}}
```

-   VUser.php

```sql
CREATE VIEW v_users AS SELECT * FROM users ORDER BY name asc;
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VUser extends Model
{
    use HasFactory;
    protected $table = 'v_users';
}
```

```php
Route::get('/', function () {
    return view('welcome');
});

Route::get('/data', function (Request $r) {
    if ($r->ajax()) {

        // $data = DB::table('v_users');                //success
        // return DataTables::of($data)                 //success

        // $data = DB::select('select * from v_users'); //error
        // return DataTables::of($data)                 //error

        $data = VUser::query();                         //success
        return DataTables::eloquent($data)              //success
            ->addColumn('action', 'partial-action')
            ->filter(function ($query) {
                if (request()->has('name')) {
                    $query->where('name', 'like', "%" . request('name') . "%");
                }

                if (request()->has('email')) {
                    $query->where('email', 'like', "%" . request('email') . "%");
                }
            }, true)
            ->order(function ($query) {
                if (request()->has('name')) {
                    $query->orderBy('name', 'asc');
                }
            })
            ->toJson();
    }
})->name('data');
```

```html
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Laravel Paging</title>
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD"
            crossorigin="anonymous"
        />
        <link
            rel="stylesheet"
            type="text/css"
            href="https://cdn.datatables.net/v/bs4/dt-1.13.2/datatables.min.css"
        />
    </head>

    <body>
        <div class="container">
            <table id="myTable" class="table table-stripped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created</th>
                        <th>Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <script
            src="https://code.jquery.com/jquery-3.6.3.min.js"
            integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU="
            crossorigin="anonymous"
        ></script>
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
            crossorigin="anonymous"
        ></script>
        <script
            type="text/javascript"
            src="https://cdn.datatables.net/v/bs4/dt-1.13.2/datatables.min.js"
        ></script>

        <script>
            $(document).ready(function () {
                var url = window.location.href;
                var jsonData = paramsToJson(url);
                $("#myTable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        type: "GET",
                        url: "{{ route('data') }}",
                        data: JSON.parse(jsonData),
                    },
                    columns: [
                        {
                            data: "id",
                            name: "id",
                        },
                        {
                            data: "name",
                            name: "name",
                        },
                        {
                            data: "email",
                            name: "email",
                        },
                        {
                            data: "created_at",
                            name: "created_at",
                        },
                        {
                            data: "updated_at",
                            name: "updated_at",
                        },
                        {
                            data: "action",
                            name: "action",
                        },
                    ],
                });
            });
        </script>

        <script>
            function paramsToJson(url) {
                var params = url.substring(url.indexOf("?"));
                var jsonData = "{";

                if (url.includes("?")) {
                    params = params.replace("?", "");
                    params = params.split("&");
                    for (var i = 0; i < params.length; i++) {
                        var d = params[i];
                        d = d.split("=");
                        jsonData += '"' + d[0] + '":"' + d[1] + '",';
                    }
                    jsonData = jsonData.slice(0, -1);
                } else {
                    params = "";
                }
                jsonData += "}";
                return jsonData;
            }
        </script>
    </body>
</html>
```
