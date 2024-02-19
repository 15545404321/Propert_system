Vue.component('Skjl', {
	template: `
		<el-drawer title="收款记录"  direction="rtl" size="1500px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
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
			Object.assign(query, {
				fcxx_id:this.info.fcxx_id,
				cewei_id:this.info.cewei_id,
				member_id:this.info.member_id
			})
			this.jump = '/shop/Syt/indexLists?' + param(query)
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
