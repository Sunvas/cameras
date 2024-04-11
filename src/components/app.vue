<template>
	<f7-app v-bind="f7params">
		<f7-page v-if="token">
			<f7-navbar :title="caption" @click="Reload" title="Tap several times to reload"></f7-navbar>

			<f7-toolbar icons bottom tabbar>
				<f7-link v-for="[title,name,icon] in streams" :href="'/'+name" :icon-material="icon" :text="title" :tab-link-active="name===active" tab-link></f7-link>
			</f7-toolbar>

			<f7-view url="/" main browserHistory browserHistorySeparator="#" :browserHistoryStoreHistory="false" :browserHistoryRoot="path"></f7-view>
		</f7-page>

		<f7-login-screen v-else v-model:opened="credentials">
			<f7-view>
				<f7-page login-screen>
					<f7-login-screen-title @click="Reload" title="Tap several times to reload">Authentication required</f7-login-screen-title>
					<f7-list form @submit="SignIn()">
						<f7-list-input
								type="text"
								name="username"
								placeholder="Login"
								autocomplete="username"
								v-model:value="login"
						></f7-list-input>
						<f7-list-input
								type="password"
								name="password"
								placeholder="Password"
								autocomplete="current-password"
								v-model:value="password"
						></f7-list-input>

						<f7-list-button title="Sign In" class="bg-color-primary" @click="SignIn()"></f7-list-button>
						<f7-block-footer>{{status}}</f7-block-footer>
					</f7-list>
				</f7-page>
			</f7-view>
		</f7-login-screen>
  </f7-app>
</template>
<style>
div { user-select:none; }
iframe {
	width: 100%;
	height: 100%;
	border: 0;
}
.login-screen-page .list-button { color: black; }
</style>
<script>
import {computed} from "vue";
import { f7 } from 'framework7-vue';

//Camera
import Cam from '../pages/cam.vue';

//404
import NotFoundPage from '../pages/404.vue';

function Routes({host,streams}){
	const routes=[{
		path: '/',
		redirect:"/"+streams[0][1],
		master:true
	}];

	for(const[title,name,,{rotate}={}] of streams)
		routes.push({
			name,
			path:"/"+name,
			options:{title,rotate,host},
			component: Cam,
		});

	//404 Error
	routes.push({
		path: '(.*)',
		name:"e404",
		component: NotFoundPage,
	});

	return routes;
}

export default {
	data:()=>({
		reload:0,//Clicks counter

		path:location.pathname,
		token:"",
		caption:"",
		active:"",
		streams:[],

		credentials:false,
		login:"",
		password:"",
		status:"",

		f7params:{
			routes:[],

			name: 'Cameras', // App name
			theme: 'auto', // Automatic theme detection
			colors: {
				primary: '#033b00',
			},
			darkMode: true,

			serviceWorker:{
				path: "service-worker.js",
				scope: location.pathname,
			},
		}
	}),
	provide:function(){
		return{
			token:computed(()=>this.token)
		};
	},
	methods:{
		/** Full reloading application with unregistering serviceWorkers */
		async Reload(){
			if(this.reload++>3)
			{
				for(const r of f7.serviceWorker.registrations)
					await f7.serviceWorker.unregister(r);

				localStorage.removeItem("auth");
				location.reload(true);
			}
		},

		/** Retrieving info abouts cameras from the cameras.json */
		async LoadCameras(auth){
			return fetch("./restricted/cameras.json",{
				headers:{Authorization:'Basic ' + auth},
				cache:this.credentials ? "reload" : "force-cache"
			})
			.then(r=>r.ok ? r.json() : null)
			.then(cameras=>{
				f7.routes.push(...Routes(cameras));

				for(const k of ["caption","streams"])
					this[k]=cameras[k];
			});
		},

		async SignIn(auth){
			auth??=btoa(this.login + ":" + this.password);

			return fetch("./restricted/",{
				headers:{Authorization:'Basic ' + auth},
				cache:"reload"
			})
			.then(r=>r.ok ? r.text() : false)
			.then(async token=>{
				if(token)
				{
					if(this.credentials)
						localStorage.setItem("auth",auth);

					await this.LoadCameras(auth);

					this.token=token;
					this.credentials=false;

					return ;
				}

				localStorage.removeItem("auth");

				this.credentials=true;

				if(this.login)
					this.status="Wrong login & password at "+ new Date().toLocaleString();
				else
					this.status="Enter login and password";
			});
		}
	},

	created(){
		const auth=localStorage.getItem("auth");

		if(auth)
			this.SignIn(auth);
		else
			this.credentials=true;
	}
}
</script>