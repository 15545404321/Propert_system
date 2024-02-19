Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">原始房主：</td>
						<td>
							{{form.member_ida}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">承接房主：</td>
						<td>
							{{form.member_idb}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">过户时间：</td>
						<td>
							{{parseTime(form.ghjl_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用结算：</td>
						<td>
							<span v-if="form.ghjl_jiesuan == '1'">原房主</span>
							<span v-if="form.ghjl_jiesuan == '2'">新房主</span>
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
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
			form:{
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Ghjl/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
