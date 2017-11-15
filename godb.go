package main

import (
	"database/sql"
	"fmt"

	_ "github.com/go-sql-driver/mysql"
)

func main() {

	db, err := sql.Open("mysql", "root:12345678@tcp(0.0.0.0:3360)/mrgenius")
	if err != nil {
		panic(err)
	}
	defer db.Close()

	// Prepare statement for reading data
	rows, err := db.Query("SELECT player_id, team_id FROM myteam_predictions")
	if err != nil {
		panic(err)
	}

	defer rows.Close()

	var id int
	var name int

	for rows.Next() {
		rows.Scan(&id, &name)
		fmt.Printf("%d : %s \n", id, name)
	}
}