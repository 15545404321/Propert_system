Vue.component('Dialogurl', {
	template: `
		<el-drawer title="批次查看"  direction="rtl" size="1500px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<iframe :src="jump" frameborder="0" width="100%" height="800px"></iframe>
			</div>
		</el-drawer>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			jump:''
		}
	},
	methods: {
		open(){
			let query = {}
			Object.assign(query, {dialogstate:1})
			Object.assign(query, {xzpici_id:this.info.xzpici_id})
			this.jump = '/shop/Gongzi/index?' + param(query)
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
