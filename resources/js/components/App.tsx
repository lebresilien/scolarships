import React, { useState } from "react";
import { render } from "react-dom";

export interface IUser {
    name: string;
    age: number;
}
const App = () => {
    const [users, setUsers] = useState<IUser[]>([
        {
            name: "Bijaya",
            age: 25,
        },
        {
            name: "Ram",
            age: 25,
        },
    ]);

    return (
        <div>
            <h1>Users list</h1>
            <ul>
                {users.map((user: IUser) => {
                    return (
                        <li key={user.name}>
                            {user.name} is {user.age} years old
                        </li>
                    );
                })}
            </ul>
        </div>
    );
};

export default App;

if(document.getElementById('root'))
{
    render(<App />, document.getElementById('root'))
}