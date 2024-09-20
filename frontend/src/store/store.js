import {router} from '../main'
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const apiUrl = 'https://vue-blog-tutorial-9ba6b-default-rtdb.firebaseio.com/';

export const store = new Vuex.Store({
    strict: true,
    state : {
        user: JSON.parse(localStorage.getItem('user')),
        users: JSON.parse(localStorage.getItem('users')),
        isLoading: false
    },
    mutations: {
        resgisterSuccess(state, user){
           state.user = user;
           state.isLoading = false;
        },
        removeSuccess(state){
            state.isLoading = false;
        },
        updateSuccess(state, user){
            state.user = user;
            state.isLoading = false;
        },
        loginSuccess(state, user){
            state.user = user;
            state.isLoading = false;
        },
        loginFailure(state){
            state.isLoading = false;
        },
        loginProcess(state){
            state.isLoading = true;
        },
        registerProcess(state){
            state.isLoading = true;
        },
        removeProcess(state){
            state.isLoading = true;
        },
        updateProcess(state){
            state.isLoading = true;
        },
        removeFailure(state){
            state.isLoading = false;
        },
        registerExist(state){
            state.isLoading = false;
        },
        registerFailure(state){
            state.isLoading = false;
        },
        updateFailure(state){
            state.isLoading = false;
        },
        getAllUserSuccess(state, users){
            state.users = users;
        }
    },
    actions: {
        register(context, user) {
            context.commit('registerProcess');
            
            // check user
            this._vm.$http.get(apiUrl + 'users.json').then(function(users) {
                return users.json();
             },  response => {
                // error
                if(response.status == 401) {
                    context.commit('registerFailure');
                    setTimeout(() => {
                        // display success message after route change completes
                        alert('Under maintenance, please try again later');
                    });
                } else{
                    context.commit('registerFailure');
                    setTimeout(() => {
                        // display success message after route change completes
                        alert('Please check again your input');
                    });
                }
            }).then(function(users){
                for (let key in users) {
                    let x = users[key];
                    if (x.email == user.email ) {
                        context.commit('registerExist');
                        setTimeout(() => {
                            alert('Email already registered');
                        });
                        return true;
                    }
                }
                return false;
             }).then(function(exist){
                if (!exist) {
                    // regis the user
                    this.$http.post(apiUrl + 'users.json', user).then(function(user) {
                        setTimeout(() => {
                            console.log(user);
                            context.commit('resgisterSuccess', user);
                            router.push('/login');
                            setTimeout(() => {
                                alert('Registration successful');
                            })
                        }, 2000); // delay effect
                    },   response => {
                        // error
                        if(response.status == 401) {
                            context.commit('registerFailure');
                            setTimeout(() => {
                                // display success message after route change completes
                                alert('Under maintenance, please try again later');
                            });
                        } else{
                            context.commit('registerFailure');
                            setTimeout(() => {
                                // display success message after route change completes
                                alert('Please check again your input');
                            });
                        }
                    });
                }
             }); 
        },
        login(context, auth) {
            context.commit('loginProcess');
            this._vm.$http.get(apiUrl + 'users.json').then(function(users) {
                // success
               return users.json();
            }, response => {
                // error
                if(response.status == 401) {
                    context.commit('loginFailure');
                    setTimeout(() => {
                        // display success message after route change completes
                        alert('Username or password is incorrect');
                    });
                } else{
                    return;
                }
            }).then(function(users) {
                for (let key in users) {
                    let x = users[key];
                    x.id = key;
                    if (x.email == auth.email && x.password == auth.password) {
                        setTimeout(() => {
                            localStorage.setItem('user', JSON.stringify(x));
                            context.commit('loginSuccess', x);
                            router.push('/home');
                            setTimeout(() => {
                                // display success message after route change completes
                                alert('Login successful, Hi ' + x.firstName + '!');
                            }, 500);
                        }, 1000);
                        return;
                    }
                }

                context.commit('loginFailure');
                setTimeout(() => {
                    // display success message after route change completes
                    alert('Username or password is incorrect');
                });
            });  
        },
        logout(){
            localStorage.removeItem('user');
            localStorage.removeItem('users');
            router.push('/login')
        },
        getAllUser(context){
            this._vm.$http.get(apiUrl + 'users.json').then(function(users) {
               return users.json();
             }).then(function(users){
                let usersData = [];
                for (let key in users) {
                    users[key].id = key;
                    usersData.push(users[key]);
                }
                localStorage.setItem('users', JSON.stringify(usersData));
                context.commit('getAllUserSuccess', usersData);
                return usersData;
             });  
        },
        removeUser(context, user){
            context.commit('removeProcess');
            if (user.email == context.state.user.email) {
                setTimeout(() => {
                    context.commit('removeFailure');
                    alert('Can\'t remove active user!');
                }, 500);
                return;
            }

            try {
                this._vm.$http.delete(apiUrl + 'users/' + user.id + '.json').then(function(user) {
                    setTimeout(() => {
                        context.commit('removeSuccess', user);
                        setTimeout(() => {
                            alert('Remove successful');
                        })
                    }); // delay effect
                }, response => {
                    // error
                    if(response.status == 401) {
                        context.commit('removeFailure');
                        setTimeout(() => {
                            // display success message after route change completes
                            alert('Under maintenance, please try again later');
                        });
                    } else{
                         context.commit('removeFailure');
                        setTimeout(() => {
                            // display success message after route change completes
                            alert('Something went wrong, report to the admin');
                        });
                    }
                } ).then(function() { 
                    context.dispatch('getAllUser');
                });
            } catch (error) {
                setTimeout(() => {
                    context.commit('removeFailure');
                    alert('Something went wrong, report to the admin');
                }, 500);
            }
        },
        updateUser(context, user){
            context.commit('updateProcess');
            let data = {
                'firstName': user.firstName,
                'lastName': user.lastName,
                'password': user.password,
            }

            this._vm.$http.patch(apiUrl + 'users/' + user.id + '.json', data).then(function(result) {
                setTimeout(() => {
                    // update data to local state
                    user.firstName = result.body.firstName;
                    user.lastName = result.body.lastName;
                    user.password = result.body.password;

                    localStorage.setItem('user', JSON.stringify(user));
                    context.commit('updateSuccess', user);
                    router.push('/');
                    setTimeout(() => {
                        alert('Update successful');
                    })
                }); // delay effect
            },  response => {
                // error
                if(response.status == 401) {
                    context.commit('updateFailure');
                    setTimeout(() => {
                        // display success message after route change completes
                        alert('Under maintenance, please try again later');
                    });
                } else{
                     context.commit('updateFailure');
                    setTimeout(() => {
                        // display success message after route change completes
                        alert('Something went wrong, report to the admin');
                    });
                }
            })
        }
    }
})