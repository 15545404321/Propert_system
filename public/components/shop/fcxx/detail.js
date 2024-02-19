Vue.component('Detail', {
	template: `
		<el-drawer title="查看详情"  direction="rtl" size="800px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<div style="margin:0 30px;">
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">房间编号：</td>
						<td>
							{{form.fcxx_fjbh}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">房主姓名：</td>
						<td>
							{{form.member_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">家庭成员：</td>
						<td>
							{{form.member_idx}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下房产：</td>
						<td>
							{{form.fcxx_fjbhx}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下车位：</td>
						<td>
							{{form.cewei_namex}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下门市：</td>
						<td>
							{{form.fcxx_ms}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下车库：</td>
						<td>
							{{form.fcxx_ck}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">名下车辆：</td>
						<td>
							{{form.car_namex}}
						</td>
					</tr>
				</tbody>
			</table>
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
			form:{
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Fcxx/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
