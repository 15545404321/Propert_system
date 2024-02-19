Vue.component('Cwfhupdate', {
	template: `
		<el-dialog title="财务审核" width="800px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="财务审核" prop="yssj_cwsh">
							<el-select style="width:100%" v-model="form.yssj_cwsh" filterable clearable placeholder="请选择财务审核">
								<el-option key="0" label="财务待审中" :value="1"></el-option>
								<el-option key="1" label="财务已确认" :value="2"></el-option>
								<el-option key="2" label="撤销复核中" :value="3"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
			},
			cbgl_ids:[],
			fylx_ids:[],
			fybz_ids:[],
			sjlx_ids:[],
			zjys_ids:[],
			lsys_ids:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Cwfh/getFieldList').then(res => {
					if(res.data.status == 200){
						this.cbgl_ids = res.data.data.cbgl_ids
						this.fylx_ids = res.data.data.fylx_ids
						this.fybz_ids = res.data.data.fybz_ids
						this.sjlx_ids = res.data.data.sjlx_ids
						this.zjys_ids = res.data.data.zjys_ids
						this.lsys_ids = res.data.data.lsys_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Cwfh/CwfhUpdate',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
