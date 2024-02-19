Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="商家名称" prop="bmdh_title">
							<el-input  v-model="form.bmdh_title" autoComplete="off" clearable  placeholder="请输入商家名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="商家介绍" prop="bmdh_neirong">
							<el-input  type="textarea" autoComplete="off" v-model="form.bmdh_neirong"  :autosize="{ minRows: 2, maxRows: 4}" clearable placeholder="请输入商家介绍"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="电话号码" prop="bmdh_tel">
							<el-input  v-model="form.bmdh_tel" autoComplete="off" clearable  placeholder="请输入电话号码"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="添加时间" prop="bmdh_date">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.bmdh_date" clearable placeholder="请输入添加时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="到期时间" prop="hmdh_end">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.hmdh_end" clearable placeholder="请输入到期时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="电话分类" prop="dhfl_id">
							<el-select   style="width:100%" v-model="form.dhfl_id" filterable clearable placeholder="请选择电话分类">
								<el-option v-for="(item,i) in dhfl_ids" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="联系人员" prop="bmdh_lxr">
							<el-input  v-model="form.bmdh_lxr" autoComplete="off" clearable  placeholder="请输入联系人员"></el-input>
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
				bmdh_title:'',
				bmdh_neirong:'',
				bmdh_tel:'',
				bmdh_date:'',
				hmdh_end:'',
				shop_id:'',
				xqgl_id:'',
				dhfl_id:'',
				bmdh_lxr:'',
			},
			dhfl_ids:[],
			loading:false,
			rules: {
				bmdh_title:[
					{required: true, message: '商家名称不能为空', trigger: 'blur'},
				],
				bmdh_tel:[
					{required: true, message: '电话号码不能为空', trigger: 'blur'},
					{pattern:/^[0-9]*$/, message: '电话号码格式错误'}
				],
				dhfl_id:[
					{required: true, message: '电话分类不能为空', trigger: 'change'},
				],
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Bmdh/getFieldList').then(res => {
					if(res.data.status == 200){
						this.dhfl_ids = res.data.data.dhfl_ids
					}
				})
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
			this.form.bmdh_date = parseTime(this.form.bmdh_date)
			this.form.hmdh_end = parseTime(this.form.hmdh_end)
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Bmdh/update',this.form).then(res => {
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
