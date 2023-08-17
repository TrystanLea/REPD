<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<style>
    .sticky-card {
        position: sticky;
        top: 20;
    }
</style>
<body>
    <div class="container-fluid" id=app>
        <div class="row">
            <!-- Side bar with field selection -->
            <div class="col-md-2">
                <div class="card mt-3 sticky-card">
                    <div class="card-header">
                        <h5>Select Fields</h5>
                    </div>
                    <ul class="list-group list-group-flush" style="overflow-x:hidden; height:600px">
                        <li class="list-group-item" v-for="column in columns">
                            <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckDefault" :checked="selected_columns.includes(column)" @click="select_column(column)">                                <label class="form-check-label" for="flexCheckDefault">
                                    {{ column }}
                                </label>
                            </div>
                        </li>
                    </ul>
                </div>

                <a class="btn btn-primary mt-3" href="map">View Map</a>
            </div>
            <dic class="col-md-10">

                <!-- two columms cards -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mt-3">
                            <div class="card-body">
                                <h4>Number of sites: {{ data.length }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mt-3">
                            <div class="card-body">
                                <h4>Total capacity: {{ total_MW | toFixed(2) }} MW</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <table class="table">
                    <tr>
                        <th v-for="column in selected_columns">{{column}}</th>
                    </tr>
                    <tr v-for="row in data">
                        <td v-for="column in selected_columns">{{row[column]}}</td>
                    </tr>
                </table>


            </dic>
        </div>
    </div>
</body>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            // list column keys
            columns: <?php echo json_encode($columns); ?>,  
            selected_columns: [
                'Site Name',
                'Installed Capacity (MWelec)',
                'Development Status',
                'Operator (or Applicant)'
            ],
            data: [],
            total_MW: 0
        },
        methods: {
            select_column: function(column) {
                if (this.selected_columns.includes(column)) {
                    this.selected_columns.splice(this.selected_columns.indexOf(column), 1);
                    return;
                }
                this.selected_columns.push(column);
            }
        },
        filters: {
            toFixed: function(value,dp) {
                if (typeof value !== "number") return value;
                return value.toFixed(dp);
            }
        }
    });

    //  load data using axios from api.php
    axios.get('api.php')
        .then(function(response) {
            app.data = response.data;

            // get total MW
            app.total_MW = 0;
            for (var i = 0; i < app.data.length; i++) {
                let capacity = parseFloat(app.data[i]['Installed Capacity (MWelec)']);
                if (isNaN(capacity)) continue;
                app.total_MW += capacity;
            }
        })
        .catch(function(error) {
            console.log(error);
        });

</script>